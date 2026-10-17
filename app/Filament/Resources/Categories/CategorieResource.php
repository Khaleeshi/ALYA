<?php

namespace App\Filament\Resources\Categories;

use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Filament\Support\ChampsTraduits;
use App\Models\Categorie;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Catégories de la boutique (Gloss, Skincare, Maquillage…). L'ordre se
 * règle en glissant les lignes ; une catégorie cachée cache ses produits.
 */
class CategorieResource extends Resource
{
    protected static ?string $model = Categorie::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|UnitEnum|null $navigationGroup = 'Catalogue';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'catégorie';

    protected static ?string $pluralModelLabel = 'catégories';

    protected static ?string $navigationLabel = 'Catégories';

    protected static ?string $slug = 'categories';

    protected static ?string $recordRouteKeyName = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                ChampsTraduits::pour(fn (string $langue, bool $principale): array => [
                    TextInput::make("nom.{$langue}")
                        ->label('Nom')
                        ->required($principale)
                        ->maxLength(60),
                    Textarea::make("description.{$langue}")
                        ->label('Phrase de présentation')
                        ->helperText('Sur la carte de la catégorie. Ex. : « Des lèvres douces, brillantes et irrésistibles. »')
                        ->rows(2)
                        ->maxLength(200),
                ]),
                TextInput::make('slug')
                    ->label('Adresse')
                    ->prefix('…/boutique/')
                    ->helperText('Laissée vide, elle se déduit du nom.')
                    ->alphaDash()
                    ->maxLength(120)
                    ->unique(ignoreRecord: true),
                SpatieMediaLibraryFileUpload::make('couverture')
                    ->label('Photo de couverture')
                    ->collection(Categorie::COUVERTURE)
                    ->image(),
                Toggle::make('en_ligne')
                    ->label('Visible sur la boutique')
                    ->helperText('Cachée, la catégorie cache aussi ses produits.')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('media')->withCount('produits'))
            ->reorderable('position')
            ->defaultSort('position')
            ->columns([
                SpatieMediaLibraryImageColumn::make('couverture')
                    ->label('')
                    ->collection(Categorie::COUVERTURE)
                    ->conversion('webp-400')
                    ->imageSize(48)
                    ->square(),
                TextColumn::make('nom')
                    ->label('Catégorie')
                    ->description(fn (Categorie $record): ?string => $record->description),
                TextColumn::make('produits_count')
                    ->label('Produits'),
                IconColumn::make('en_ligne')
                    ->label('Visible')
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->paginated(false);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCategories::route('/'),
        ];
    }
}
