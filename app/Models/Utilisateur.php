<?php

namespace App\Models;

use App\Support\Acces\Roles;
use Database\Factories\UtilisateurFactory;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthentication;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * Membre du personnel : propriétaire, administrateur ou livreur (garde « personnel »).
 *
 * @property int $id
 * @property string $prenom
 * @property string $nom
 * @property string $email
 * @property bool $actif
 */
class Utilisateur extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery, HasName
{
    /** @use HasFactory<UtilisateurFactory> */
    use HasFactory, HasRoles, InteractsWithAppAuthentication, InteractsWithAppAuthenticationRecovery, Notifiable;

    protected $table = 'utilisateurs';

    /** Garde utilisée par spatie/laravel-permission. */
    protected $guard_name = Roles::GARDE;

    protected $fillable = [
        'prenom',
        'nom',
        'email',
        'telephone',
        'password',
        'actif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'actif' => 'boolean',
            'derniere_connexion_le' => 'datetime',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->actif && $this->hasAnyRole(Roles::administration());
    }

    public function getFilamentName(): string
    {
        return $this->nom_complet;
    }

    public function estProprietaire(): bool
    {
        return $this->hasRole(Roles::PROPRIETAIRE);
    }

    /**
     * Nom qui signe les lignes du journal d'activité : « Propriétaire · Ndeye »,
     * « Administrateur · Awa » ou « Livreur · Moussa ».
     */
    public function libelleJournal(): string
    {
        $role = $this->getRoleNames()->first();

        return (Roles::libelles()[$role] ?? 'Personnel').' · '.$this->prenom;
    }

    public function getAppAuthenticationHolderName(): string
    {
        return $this->email;
    }

    protected function nomComplet(): Attribute
    {
        return Attribute::get(fn (): string => trim($this->prenom.' '.$this->nom));
    }

    protected function email(): Attribute
    {
        return Attribute::set(fn (string $valeur): string => mb_strtolower(trim($valeur)));
    }
}
