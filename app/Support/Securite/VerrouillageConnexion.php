<?php

namespace App\Support\Securite;

use App\Support\Telephone;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\LoginRateLimiter;

/**
 * Verrouillage après cinq échecs de connexion (audit 4.8, recette n° 16).
 *
 * Cinq mots de passe faux pour le même identifiant depuis la même adresse
 * bloquent la connexion pendant quinze minutes, même avec le bon mot de passe.
 * Une connexion réussie remet le compteur à zéro.
 *
 * Fortify utilise cette classe à la place de son limiteur (une minute par défaut) ;
 * la connexion à l'administration applique les mêmes règles.
 */
class VerrouillageConnexion extends LoginRateLimiter
{
    public const ESSAIS = 5;

    public const DUREE_SECONDES = 15 * 60;

    public function tooManyAttempts(Request $request)
    {
        return $this->limiter->tooManyAttempts($this->throttleKey($request), self::ESSAIS);
    }

    public function increment(Request $request)
    {
        $this->limiter->hit($this->throttleKey($request), self::DUREE_SECONDES);
    }

    protected function throttleKey(Request $request)
    {
        return self::cle('web', (string) $request->input(Fortify::username()), $request->ip());
    }

    /**
     * Clé du compteur pour un espace (« web » pour les clientes, « personnel »
     * pour l'administration). Un même téléphone saisi de deux façons donne la même clé.
     */
    public static function cle(string $espace, string $identifiant, ?string $ip): string
    {
        $identifiant = trim($identifiant);

        if (! str_contains($identifiant, '@')) {
            $identifiant = Telephone::normaliser($identifiant) ?? $identifiant;
        }

        return 'connexion|'.$espace.'|'.Str::transliterate(Str::lower($identifiant)).'|'.$ip;
    }
}
