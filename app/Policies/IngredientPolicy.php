<?php

namespace App\Policies;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Model;

/**
 * Un ingrédient se supprime seulement s'il n'est plus sur aucune fiche.
 */
class IngredientPolicy extends PolitiqueCatalogue
{
    protected function peutDisparaitre(Model $element): bool
    {
        /** @var Ingredient $element */
        return ! $element->produits()->exists();
    }
}
