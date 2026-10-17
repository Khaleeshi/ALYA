<?php

namespace App\Filament\Resources\Produits\RelationManagers;

use App\Models\MouvementStock;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Historique du stock sous la fiche produit : lecture seule.
 */
class MouvementsStockRelationManager extends RelationManager
{
    protected static string $relationship = 'mouvementsStock';

    protected static ?string $title = 'Historique du stock';

    protected static ?string $modelLabel = 'mouvement';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['variante', 'utilisateur']))
            ->columns([
                TextColumn::make('cree_le')
                    ->label('Date')
                    ->dateTime(),
                TextColumn::make('variante.nom')
                    ->label('Couleur ou format')
                    ->placeholder('Produit sans choix'),
                TextColumn::make('quantite')
                    ->label('Mouvement')
                    ->formatStateUsing(fn (int $state): string => sprintf('%+d', $state))
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'danger'),
                TextColumn::make('stock_apres')
                    ->label('Stock après'),
                TextColumn::make('motif')
                    ->label('Motif')
                    ->formatStateUsing(fn (string $state): string => MouvementStock::motifs()[$state] ?? $state),
                TextColumn::make('note')
                    ->label('Note')
                    ->placeholder('—')
                    ->wrap(),
                TextColumn::make('utilisateur.nom_complet')
                    ->label('Par')
                    ->placeholder('Système'),
            ])
            ->paginated([10, 25, 50]);
    }
}
