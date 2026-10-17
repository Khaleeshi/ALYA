<?php

namespace App\Filament\Resources\Produits\Pages;

use App\Filament\Resources\Produits\ProduitResource;
use App\Models\JournalActivite;
use App\Models\Produit;
use Filament\Resources\Pages\CreateRecord;

class CreateProduit extends CreateRecord
{
    protected static string $resource = ProduitResource::class;

    protected function afterCreate(): void
    {
        /** @var Produit $produit */
        $produit = $this->record;

        // Variantes, ingrédients et effets sont enregistrés après le produit.
        $produit->actualiserTexteRecherche();

        JournalActivite::consigner(
            'catalogue.creation',
            sprintf('Création du produit « %s » (%s)', $produit->nom, Produit::statuts()[$produit->statut]),
            $produit,
        );
    }
}
