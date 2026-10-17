<?php

namespace App\Listeners;

use App\Models\JournalActivite;
use App\Models\Utilisateur;
use App\Support\Securite\VerrouillageConnexion;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Events\Dispatcher;
use Laravel\Fortify\Fortify;

/**
 * Inscrit au journal les connexions et déconnexions du personnel, et chaque
 * verrouillage après cinq échecs. Les connexions des clientes ne sont pas
 * journalisées une à une : elles n'ont rien d'une action de gestion.
 */
class JournaliserAuthentification
{
    public function connexion(Login $evenement): void
    {
        if (! $evenement->user instanceof Utilisateur) {
            return;
        }

        $evenement->user->forceFill(['derniere_connexion_le' => now()])->saveQuietly();

        JournalActivite::consigner('personnel.connexion', "Connexion à l'administration", $evenement->user, auteur: $evenement->user);
    }

    public function deconnexion(Logout $evenement): void
    {
        if (! $evenement->user instanceof Utilisateur) {
            return;
        }

        JournalActivite::consigner('personnel.deconnexion', "Déconnexion de l'administration", $evenement->user, auteur: $evenement->user);
    }

    public function verrouillage(Lockout $evenement): void
    {
        self::consignerVerrouillage('boutique', (string) $evenement->request->input(Fortify::username()));
    }

    public static function consignerVerrouillage(string $espace, string $identifiant): void
    {
        JournalActivite::consigner(
            'connexion.verrouillage',
            sprintf('Connexion verrouillée %d minutes après %d échecs (%s)', VerrouillageConnexion::DUREE_SECONDES / 60, VerrouillageConnexion::ESSAIS, $espace),
            donnees: ['espace' => $espace, 'identifiant' => mb_strtolower(trim($identifiant))],
        );
    }

    /**
     * @return array<class-string, string>
     */
    public function subscribe(Dispatcher $evenements): array
    {
        return [
            Login::class => 'connexion',
            Logout::class => 'deconnexion',
            Lockout::class => 'verrouillage',
        ];
    }
}
