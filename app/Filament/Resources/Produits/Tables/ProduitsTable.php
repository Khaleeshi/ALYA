<?php

namespace App\Filament\Resources\Produits\Tables;

use App\Filament\Resources\Produits\Actions\AjusterStockAction;
use App\Models\Categorie;
use App\Models\Produit;
use App\Support\Montant;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProduitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->with(['categorie', 'media'])
                ->withSum('variantes as stock_total', 'stock'))
            ->defaultSort('updated_at', 'desc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('photos')
                    ->label('')
                    ->collection(Produit::PHOTOS)
                    ->conversion('webp-400')
                    ->limit(1)
                    ->imageSize(48)
                    ->square(),
                TextColumn::make('nom')
                    ->label('Produit')
                    ->description(fn (Produit $record): string => $record->categorie->nom)
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query->recherche($search)),
                TextColumn::make('prix')
                    ->label('Prix')
                    ->formatStateUsing(fn (int $state): string => Montant::fcfa($state))
                    ->sortable(),
                TextColumn::make('stock_total')
                    ->label('Stock')
                    ->badge()
                    ->color(fn (?int $state, Produit $record): string => match (true) {
                        ($state ?? 0) <= 0 => 'danger',
                        $state <= $record->seuil_alerte => 'warning',
                        default => 'success',
                    })
                    ->sortable(),
                TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Produit::statuts()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        Produit::PUBLIE => 'success',
                        Produit::ARCHIVE => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('nature')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state, Produit $record): string => Produit::natures()[$state].($record->edition_limitee ? ' · édition limitée' : ''))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Modifié')
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('categorie_id')
                    ->label('Catégorie')
                    ->options(fn (): array => Categorie::query()->orderBy('position')->get()->mapWithKeys(fn (Categorie $categorie): array => [$categorie->id => $categorie->nom])->all()),
                SelectFilter::make('statut')
                    ->label('Statut')
                    ->options(Produit::statuts()),
                SelectFilter::make('nature')
                    ->label('Type')
                    ->options(Produit::natures()),
                TernaryFilter::make('edition_limitee')
                    ->label('Édition limitée'),
                Filter::make('stock_faible')
                    ->label('Stock faible ou épuisé')
                    ->query(fn (Builder $query): Builder => $query->whereHas('variantes', fn (Builder $variantes) => $variantes
                        ->where('active', true)
                        ->whereColumn('variantes.stock', '<=', 'produits.seuil_alerte'))),
            ])
            ->recordActions([
                EditAction::make(),
                AjusterStockAction::make(),
            ])
            ->emptyStateHeading('Aucun produit pour l’instant')
            ->emptyStateDescription('Ajoutez le premier produit du catalogue AMANI.');
    }
}
