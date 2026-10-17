<?php

namespace App\Models;

use App\Support\Telephone;
use Database\Factories\ClienteFactory;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * Cliente de la boutique (garde « web »). Son compte repose sur son numéro
 * de téléphone, vérifié par un code SMS : il n'y a pas de mot de passe.
 *
 * @property int $id
 * @property string $prenom
 * @property string $nom
 * @property string $telephone
 * @property ?Carbon $telephone_verifie_le
 * @property ?string $email
 * @property string $langue
 */
class Cliente extends Authenticatable implements HasLocalePreference
{
    /** @use HasFactory<ClienteFactory> */
    use HasFactory, Notifiable;

    protected $table = 'clientes';

    protected $fillable = [
        'prenom',
        'nom',
        'telephone',
        'email',
        'langue',
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'telephone_verifie_le' => 'datetime',
        ];
    }

    /**
     * @return HasMany<Consentement, $this>
     */
    public function consentements(): HasMany
    {
        return $this->hasMany(Consentement::class);
    }

    /**
     * Dernier choix connu pour un type d'accord. Sans choix enregistré : non.
     */
    public function aConsenti(string $type): bool
    {
        return (bool) $this->consentements()
            ->where('type', $type)
            ->latest('cree_le')
            ->latest('id')
            ->value('accorde');
    }

    public function enregistrerConsentement(string $type, bool $accorde, string $origine, ?string $adresseIp = null): Consentement
    {
        return $this->consentements()->create([
            'type' => $type,
            'accorde' => $accorde,
            'origine' => $origine,
            'adresse_ip' => $adresseIp,
        ]);
    }

    /**
     * Langue des messages envoyés à la cliente.
     */
    public function preferredLocale(): string
    {
        return $this->langue;
    }

    protected function nomComplet(): Attribute
    {
        return Attribute::get(fn (): string => trim($this->prenom.' '.$this->nom));
    }

    protected function telephone(): Attribute
    {
        return Attribute::set(fn (?string $valeur): ?string => Telephone::normaliser($valeur) ?? $valeur);
    }

    protected function email(): Attribute
    {
        return Attribute::set(fn (?string $valeur): ?string => filled($valeur) ? mb_strtolower(trim($valeur)) : null);
    }
}
