<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Produits\ProduitResource;
use App\Models\Produit;
use App\Models\Variante;
use App\Support\Acces\Permissions;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * Alertes de stock faible (cahier §12.1) : couleurs et formats en vente
 * dont le stock est au seuil d'alerte du produit ou en dessous.
 */
class StockFaible extends TableWidget
{
    protected static ?string $heading = 'Stock faible';

    protected int|string|array $columnSpan = 'full';

    protected static bool $isDiscovered = false;

    public static function canView(): bool
    {
        return (bool) auth('personnel')->user()?->can(Permissions::CATALOGUE_GERER);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Variante::query()
                ->select('variantes.*')
                ->join('produits', 'produits.id', '=', 'variantes.produit_id')
                ->where('variantes.active', true)
                ->where('produits.statut', '!=', Produit::ARCHIVE)
                ->whereColumn('variantes.stock', '<=', 'produits.seuil_alerte')
                ->with('produit'))
            ->defaultSort('variantes.stock')
            ->columns([
                TextColumn::make('libelle')
                    ->label('Produit')
                    ->state(fn (Variante $record): string => $record->libelle()),
                TextColumn::make('reference')
                    ->label('Référence'),
                TextColumn::make('stock')
                    ->label('Stock')
                    ->badge()
                    ->color(fn (int $state): string => $state <= 0 ? 'danger' : 'warning')
                    ->formatStateUsing(fn (int $state): string => $state <= 0 ? 'Épuisé' : (string) $state),
            ])
            ->recordUrl(fn (Variante $record): string => ProduitResource::getUrl('edit', ['record' => $record->produit]))
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->emptyStateIcon(null)
            ->emptyStateHeading('Aucune alerte de stock')
            ->emptyStateDescription('Les produits au seuil d’alerte ou épuisés apparaîtront ici.');
    }
}
