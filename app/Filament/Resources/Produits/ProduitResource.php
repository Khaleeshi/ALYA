<?php

namespace App\Filament\Resources\Produits;

use App\Filament\Resources\Produits\Pages\CreateProduit;
use App\Filament\Resources\Produits\Pages\EditProduit;
use App\Filament\Resources\Produits\Pages\ListProduits;
use App\Filament\Resources\Produits\RelationManagers\MouvementsStockRelationManager;
use App\Filament\Resources\Produits\Schemas\ProduitForm;
use App\Filament\Resources\Produits\Tables\ProduitsTable;
use App\Models\Produit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

/**
 * Produits (cahier §12.2) : création, édition, variantes, photos, prix,
 * catégories, stock et visibilité. Permission catalogue.gerer.
 */
class ProduitResource extends Resource
{
    protected static ?string $model = Produit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static string|UnitEnum|null $navigationGroup = 'Catalogue';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'produit';

    protected static ?string $pluralModelLabel = 'produits';

    protected static ?string $navigationLabel = 'Produits';

    protected static ?string $slug = 'produits';

    // Les adresses de l'administration gardent le numéro : le slug peut changer.
    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $recordTitleAttribute = 'nom';

    /** Recherche du haut de l'administration : la même que la boutique (sans accents ni majuscules). */
    public static function getGloballySearchableAttributes(): array
    {
        return ['texte_recherche'];
    }

    protected static function applyGlobalSearchAttributeConstraints(Builder $query, string $search): void
    {
        $query->recherche($search);
    }

    public static function form(Schema $schema): Schema
    {
        return ProduitForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProduitsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            MouvementsStockRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProduits::route('/'),
            'create' => CreateProduit::route('/nouveau'),
            'edit' => EditProduit::route('/{record}/modifier'),
        ];
    }
}
