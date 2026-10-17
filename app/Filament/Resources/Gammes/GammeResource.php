<?php

namespace App\Filament\Resources\Gammes;

use App\Filament\Resources\Gammes\Pages\ManageGammes;
use App\Filament\Support\ChampsTraduits;
use App\Models\Gamme;
use App\Models\Produit;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Collections (cahier §12.2, maquette 3) : permanentes, saisonnières,
 * éditions limitées ; ordre d'affichage et dates. « Gamme » dans le code.
 */
class GammeResource extends Resource
{
    protected static ?string $model = Gamme::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Catalogue';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'collection';

    protected static ?string $pluralModelLabel = 'collections';

    protected static ?string $navigationLabel = 'Collections';

    protected static ?string $slug = 'collections';

    protected static ?string $recordRouteKeyName = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                ChampsTraduits::pour(fn (string $langue, bool $principale): array => [
                    TextInput::make("nom.{$langue}")
                        ->label('Nom')
                        ->placeholder('Ex. : Rose Élégance')
                        ->required($principale)
                        ->maxLength(80),
                    TextInput::make("accroche.{$langue}")
                        ->label('Accroche')
                        ->placeholder('Ex. : Des teintes uniques pour briller autrement.')
                        ->maxLength(120),
                    Textarea::make("description.{$langue}")
                        ->label('Présentation')
                        ->helperText('En haut de la page de la collection.')
                        ->rows(3)
                        ->maxLength(1000),
                ]),
                Radio::make('type')
                    ->label('Type')
                    ->options(Gamme::types())
                    ->default(Gamme::PERMANENTE)
                    ->inline()
                    ->required(),
                Select::make('produits')
                    ->label('Produits de la collection')
                    ->relationship('produits', 'nom')
                    ->getOptionLabelFromRecordUsing(fn (Produit $produit): string => $produit->nom)
                    ->multiple()
                    ->preload(),
                SpatieMediaLibraryFileUpload::make('couverture')
                    ->label('Photo de couverture')
                    ->collection(Gamme::COUVERTURE)
                    ->image(),
                Grid::make(2)->schema([
                    DateTimePicker::make('debut_le')
                        ->label('Visible à partir du')
                        ->native(false)
                        ->displayFormat('d/m/Y H:i')
                        ->seconds(false),
                    DateTimePicker::make('fin_le')
                        ->label('Jusqu’au')
                        ->native(false)
                        ->displayFormat('d/m/Y H:i')
                        ->seconds(false)
                        ->minDate(fn (Get $get): mixed => $get('debut_le')),
                ]),
                TextInput::make('slug')
                    ->label('Adresse')
                    ->prefix('…/collections/')
                    ->helperText('Laissée vide, elle se déduit du nom.')
                    ->alphaDash()
                    ->maxLength(120)
                    ->unique(ignoreRecord: true),
                Toggle::make('en_ligne')
                    ->label('Visible sur la boutique')
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
                    ->collection(Gamme::COUVERTURE)
                    ->conversion('webp-400')
                    ->imageSize(48)
                    ->square(),
                TextColumn::make('nom')
                    ->label('Collection')
                    ->description(fn (Gamme $record): ?string => $record->accroche),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state): string => Gamme::types()[$state] ?? $state),
                TextColumn::make('produits_count')
                    ->label('Produits'),
                TextColumn::make('fin_le')
                    ->label('Jusqu’au')
                    ->dateTime()
                    ->placeholder('—'),
                IconColumn::make('en_ligne')
                    ->label('Visible')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options(Gamme::types()),
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
            'index' => ManageGammes::route('/'),
        ];
    }
}
