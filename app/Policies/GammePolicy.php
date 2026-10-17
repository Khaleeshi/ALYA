<?php

namespace App\Policies;

use Illuminate\Database\Eloquent\Model;

/**
 * Une collection ne fait que regrouper des produits : la supprimer ne
 * touche pas aux produits.
 */
class GammePolicy extends PolitiqueCatalogue
{
    protected function peutDisparaitre(Model $element): bool
    {
        return true;
    }
}
