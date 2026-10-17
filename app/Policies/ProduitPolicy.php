<?php

namespace App\Policies;

use App\Models\Produit;
use Illuminate\Database\Eloquent\Model;

/**
 * Un produit qui a déjà été en ligne ne se supprime pas : on l'archive, pour
 * garder son historique (commandes, avis, stock). Seul un brouillon jamais
 * publié peut disparaître.
 */
class ProduitPolicy extends PolitiqueCatalogue
{
    protected function peutDisparaitre(Model $element): bool
    {
        /** @var Produit $element */
        return $element->publie_le === null;
    }
}
