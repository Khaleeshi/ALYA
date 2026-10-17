<?php

namespace App\Support\Comptes;

use App\Models\CodeConnexion;
use App\Support\Sms\EnvoiSms;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Codes de connexion par SMS (cahier §6.2 et §15) : six chiffres, valables
 * dix minutes, utilisables une seule fois, cinq essais au plus par code.
 * Les envois sont limités pour protéger la cliente et le budget SMS :
 * un renvoi par minute, trois codes par numéro par quart d'heure, dix codes
 * par adresse IP par heure. Le verrouillage après cinq codes faux est géré
 * par VerrouillageConnexion.
 */
class CodesConnexion
{
    public const CHIFFRES = 6;

    public const VALIDITE_MINUTES = 10;

    public const ESSAIS_PAR_CODE = 5;

    public const DELAI_RENVOI_SECONDES = 60;

    public const ENVOIS_PAR_NUMERO = 3;

    public const ENVOIS_PAR_IP = 10;

    public function __construct(private readonly EnvoiSms $sms) {}

    /**
     * Crée un nouveau code, rend caducs les précédents et l'envoie par SMS.
     *
     * @throws EnvoiRefuse
     */
    public function envoyer(string $telephone, ?string $adresseIp): void
    {
        $cleDelai = 'sms-code|delai|'.$telephone;
        $cleNumero = 'sms-code|numero|'.$telephone;
        $cleIp = 'sms-code|ip|'.$adresseIp;

        if (RateLimiter::tooManyAttempts($cleDelai, 1)) {
            throw new EnvoiRefuse(__('comptes.code.attendre', ['secondes' => RateLimiter::availableIn($cleDelai)]));
        }

        foreach ([$cleNumero => self::ENVOIS_PAR_NUMERO, $cleIp => self::ENVOIS_PAR_IP] as $cle => $maximum) {
            if (RateLimiter::tooManyAttempts($cle, $maximum)) {
                throw new EnvoiRefuse(__('comptes.code.trop_envois', ['minutes' => (int) ceil(RateLimiter::availableIn($cle) / 60)]));
            }
        }

        RateLimiter::hit($cleDelai, self::DELAI_RENVOI_SECONDES);
        RateLimiter::hit($cleNumero, 15 * 60);
        RateLimiter::hit($cleIp, 60 * 60);

        $code = str_pad((string) random_int(0, 10 ** self::CHIFFRES - 1), self::CHIFFRES, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($telephone, $code, $adresseIp): void {
            CodeConnexion::where('telephone', $telephone)
                ->whereNull('utilise_le')
                ->where('expire_le', '>', now())
                ->update(['expire_le' => now()]);

            CodeConnexion::create([
                'telephone' => $telephone,
                'empreinte' => self::empreinte($telephone, $code),
                'expire_le' => now()->addMinutes(self::VALIDITE_MINUTES),
                'adresse_ip' => $adresseIp,
            ]);
        });

        $this->sms->envoyer($telephone, __('comptes.sms.code', ['code' => $code, 'minutes' => self::VALIDITE_MINUTES]));
    }

    /**
     * Vérifie un code. Le bon code est consommé : il ne sert qu'une fois.
     */
    public function verifier(string $telephone, string $code): bool
    {
        return DB::transaction(function () use ($telephone, $code): bool {
            $ligne = CodeConnexion::where('telephone', $telephone)
                ->whereNull('utilise_le')
                ->where('expire_le', '>', now())
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if ($ligne === null || $ligne->tentatives >= self::ESSAIS_PAR_CODE) {
                return false;
            }

            if (! hash_equals($ligne->empreinte, self::empreinte($telephone, $code))) {
                $ligne->increment('tentatives');

                return false;
            }

            $ligne->forceFill(['utilise_le' => now()])->save();

            return true;
        });
    }

    private static function empreinte(string $telephone, string $code): string
    {
        return hash_hmac('sha256', $telephone.'|'.$code, (string) config('app.key'));
    }
}
