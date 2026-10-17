<?php

namespace App\Policies;

use App\Models\Utilisateur;
use App\Support\Acces\Permissions;
use Illuminate\Database\Eloquent\Model;

/**
 * Base commune du catalogue : tout se fait avec la permission
 * catalogue.gerer. Chaque élément précise quand on peut le supprimer.
 */
abstract class PolitiqueCatalogue
{
    public function viewAny(Utilisateur $personne): bool
    {
        return $personne->can(Permissions::CATALOGUE_GERER);
    }

    public function view(Utilisateur $personne, Model $element): bool
    {
        return $personne->can(Permissions::CATALOGUE_GERER);
    }

    public function create(Utilisateur $personne): bool
    {
        return $personne->can(Permissions::CATALOGUE_GERER);
    }

    public function update(Utilisateur $personne, Model $element): bool
    {
        return $personne->can(Permissions::CATALOGUE_GERER);
    }

    public function delete(Utilisateur $personne, Model $element): bool
    {
        return $personne->can(Permissions::CATALOGUE_GERER) && $this->peutDisparaitre($element);
    }

    /** Les suppressions se font une à une, avec leur vérification. */
    public function deleteAny(Utilisateur $personne): bool
    {
        return false;
    }

    abstract protected function peutDisparaitre(Model $element): bool;
}
