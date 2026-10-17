<?php

namespace App\Policies;

use App\Models\Utilisateur;
use App\Support\Acces\Permissions;

/**
 * Gestion du personnel : réservée à la permission utilisateurs.gerer
 * (la propriétaire par défaut). On ne supprime jamais un compte : on le
 * désactive, pour que le journal garde un auteur.
 */
class UtilisateurPolicy
{
    public function viewAny(Utilisateur $personne): bool
    {
        return $personne->can(Permissions::UTILISATEURS_GERER);
    }

    public function view(Utilisateur $personne, Utilisateur $utilisateur): bool
    {
        return $personne->can(Permissions::UTILISATEURS_GERER);
    }

    public function create(Utilisateur $personne): bool
    {
        return $personne->can(Permissions::UTILISATEURS_GERER);
    }

    public function update(Utilisateur $personne, Utilisateur $utilisateur): bool
    {
        // Seule une propriétaire modifie le compte d'une propriétaire.
        if ($utilisateur->estProprietaire() && ! $personne->estProprietaire()) {
            return false;
        }

        return $personne->can(Permissions::UTILISATEURS_GERER);
    }

    public function delete(Utilisateur $personne, Utilisateur $utilisateur): bool
    {
        return false;
    }

    public function deleteAny(Utilisateur $personne): bool
    {
        return false;
    }
}
