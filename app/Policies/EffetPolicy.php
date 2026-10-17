<?php

namespace App\Policies;

use App\Models\Effet;
use Illuminate\Database\Eloquent\Model;

/**
 * Un effet se supprime seulement s'il n'est plus sur aucune fiche.
 */
class EffetPolicy extends PolitiqueCatalogue
{
    protected function peutDisparaitre(Model $element): bool
    {
        /** @var Effet $element */
        return ! $element->produits()->exists();
    }
}
