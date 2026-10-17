<?php

namespace App\Filament\Resources\Effets;

use App\Filament\Resources\Effets\Pages\ManageEffets;
use App\Filament\Support\ChampsTraduits;
use App\Models\Effet;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Effets et besoins (hydratation, éclat, tenue…) : filtre « effet/besoin »
 * de la boutique (cahier §5.2).
 */
class EffetResource extends Resource
{
    protected static ?string $model = Effet::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|UnitEnum|null $navigationGroup = 'Catalogue';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'effet';

    protected static ?string $pluralModelLabel = 'effets et besoins';

    protected static ?string $navigationLabel = 'Effets et besoins';

    protected static ?string $slug = 'effets';

    protected static ?string $recordRouteKeyName = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                ChampsTraduits::pour(fn (string $langue, bool $principale): array => [
                    TextInput::make("nom.{$langue}")
                        ->label('Nom')
                        ->placeholder('Ex. : Hydratation')
                        ->required($principale)
                        ->maxLength(60),
                ]),
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
            ->modifyQueryUsing(fn ($query) => $query->withCount('produits'))
            ->reorderable('position')
            ->defaultSort('position')
            ->columns([
                TextColumn::make('nom')
                    ->label('Effet ou besoin'),
                TextColumn::make('produits_count')
                    ->label('Produits'),
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
            'index' => ManageEffets::route('/'),
        ];
    }
}
