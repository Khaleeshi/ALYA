<?php

namespace App\Filament\Resources\Utilisateurs;

use App\Filament\Resources\Utilisateurs\Pages\CreateUtilisateur;
use App\Filament\Resources\Utilisateurs\Pages\EditUtilisateur;
use App\Filament\Resources\Utilisateurs\Pages\ListUtilisateurs;
use App\Filament\Resources\Utilisateurs\Schemas\UtilisateurForm;
use App\Filament\Resources\Utilisateurs\Tables\UtilisateursTable;
use App\Models\Utilisateur;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

/**
 * Personnel : création des comptes, rôle, permissions en plus du rôle,
 * activation. Réservé à la permission utilisateurs.gerer.
 */
class UtilisateurResource extends Resource
{
    protected static ?string $model = Utilisateur::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Paramètres';

    protected static ?int $navigationSort = 10;

    protected static ?string $modelLabel = 'membre du personnel';

    protected static ?string $pluralModelLabel = 'personnel';

    protected static ?string $navigationLabel = 'Personnel';

    protected static ?string $slug = 'personnel';

    protected static ?string $recordTitleAttribute = 'nom_complet';

    /** « nom_complet » n'est pas une colonne : la recherche du haut porte sur ses morceaux. */
    public static function getGloballySearchableAttributes(): array
    {
        return ['prenom', 'nom', 'email'];
    }

    public static function form(Schema $schema): Schema
    {
        return UtilisateurForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UtilisateursTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('roles');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUtilisateurs::route('/'),
            'create' => CreateUtilisateur::route('/nouveau'),
            'edit' => EditUtilisateur::route('/{record}/modifier'),
        ];
    }
}
