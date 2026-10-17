<?php

namespace App\Policies;

use App\Models\JournalActivite;
use App\Models\Utilisateur;
use App\Support\Acces\Permissions;

/**
 * Le journal se consulte avec la permission journal.voir. Personne ne le
 * crée à la main, ne le modifie ni ne le supprime, pas même la dirigeante.
 */
class JournalActivitePolicy
{
    public function viewAny(Utilisateur $personne): bool
    {
        return $personne->can(Permissions::JOURNAL_VOIR);
    }

    public function view(Utilisateur $personne, JournalActivite $ligne): bool
    {
        return $personne->can(Permissions::JOURNAL_VOIR);
    }

    public function create(Utilisateur $personne): bool
    {
        return false;
    }

    public function update(Utilisateur $personne, JournalActivite $ligne): bool
    {
        return false;
    }

    public function delete(Utilisateur $personne, JournalActivite $ligne): bool
    {
        return false;
    }

    public function deleteAny(Utilisateur $personne): bool
    {
        return false;
    }
}
