<?php

namespace App\Filament\Resources\Ingredients;

use App\Filament\Resources\Ingredients\Pages\ManageIngredients;
use App\Filament\Support\ChampsTraduits;
use App\Models\Ingredient;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Ingrédients clés (maquette 2) : photo ronde, nom, bienfait. Un même
 * ingrédient sert à plusieurs fiches et au filtre de la boutique.
 */
class IngredientResource extends Resource
{
    protected static ?string $model = Ingredient::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

    protected static string|UnitEnum|null $navigationGroup = 'Catalogue';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'ingrédient';

    protected static ?string $pluralModelLabel = 'ingrédients';

    protected static ?string $navigationLabel = 'Ingrédients clés';

    protected static ?string $slug = 'ingredients';

    protected static ?string $recordRouteKeyName = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                ChampsTraduits::pour(fn (string $langue, bool $principale): array => [
                    TextInput::make("nom.{$langue}")
                        ->label('Nom')
                        ->placeholder('Ex. : Huile de jojoba')
                        ->required($principale)
                        ->maxLength(60),
                    TextInput::make("bienfait.{$langue}")
                        ->label('Bienfait en quelques mots')
                        ->placeholder('Ex. : Nourrit et protège')
                        ->maxLength(60),
                ]),
                SpatieMediaLibraryFileUpload::make('photo')
                    ->label('Photo')
                    ->helperText('Affichée en rond : l’ingrédient bien centré.')
                    ->collection(Ingredient::PHOTO)
                    ->image(),
                TextInput::make('slug')
                    ->label('Adresse du filtre')
                    ->helperText('Laissée vide, elle se déduit du nom.')
                    ->alphaDash()
                    ->maxLength(120)
                    ->unique(ignoreRecord: true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('media')->withCount('produits'))
            ->defaultSort('id')
            ->columns([
                SpatieMediaLibraryImageColumn::make('photo')
                    ->label('')
                    ->collection(Ingredient::PHOTO)
                    ->conversion('webp-400')
                    ->imageSize(44)
                    ->circular(),
                TextColumn::make('nom')
                    ->label('Ingrédient')
                    ->description(fn (Ingredient $record): ?string => $record->bienfait),
                TextColumn::make('produits_count')
                    ->label('Produits'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageIngredients::route('/'),
        ];
    }
}
