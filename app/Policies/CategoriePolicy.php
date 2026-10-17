<?php

namespace App\Policies;

use App\Models\Categorie;
use Illuminate\Database\Eloquent\Model;

/**
 * Une catégorie se supprime seulement vide ; sinon, on la cache.
 */
class CategoriePolicy extends PolitiqueCatalogue
{
    protected function peutDisparaitre(Model $element): bool
    {
        /** @var Categorie $element */
        return ! $element->produits()->exists();
    }
}
