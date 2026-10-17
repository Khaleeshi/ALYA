<?php

namespace App\Filament\Resources\Produits\Pages;

use App\Filament\Resources\Produits\Actions\AjusterStockAction;
use App\Filament\Resources\Produits\ProduitResource;
use App\Models\JournalActivite;
use App\Models\Produit;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Route;

class EditProduit extends EditRecord
{
    protected static string $resource = ProduitResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Produit $produit */
        $produit = $this->record;

        return [
            Action::make('voir')
                ->label('Voir sur la boutique')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->color('gray')
                ->url(fn (): string => route_langue('boutique.produit', ['produit' => $produit->slug]), shouldOpenInNewTab: true)
                ->visible(fn (): bool => Route::has('fr.boutique.produit') && $produit->estVisible()),
            AjusterStockAction::make()
                // Le stock affiché dans le formulaire suit le mouvement enregistré.
                ->after(function (): void {
                    $this->record->refresh();
                    $this->fillForm();
                }),
            DeleteAction::make()
                ->modalDescription('Ce brouillon n’a jamais été en ligne : il disparaît avec ses photos et ses variantes.')
                ->before(fn (Produit $record) => JournalActivite::consigner(
                    'catalogue.suppression',
                    sprintf('Suppression du brouillon « %s »', $record->nom),
                    $record,
                )),
        ];
    }

    protected function afterSave(): void
    {
        /** @var Produit $produit */
        $produit = $this->record;
        $produit->actualiserTexteRecherche();
    }
}
