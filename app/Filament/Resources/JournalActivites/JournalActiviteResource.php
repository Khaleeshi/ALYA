<?php

namespace App\Filament\Resources\JournalActivites;

use App\Filament\Resources\JournalActivites\Pages\ListJournalActivites;
use App\Filament\Resources\JournalActivites\Pages\ViewJournalActivite;
use App\Filament\Resources\JournalActivites\Schemas\JournalActiviteInfolist;
use App\Filament\Resources\JournalActivites\Tables\JournalActivitesTable;
use App\Models\JournalActivite;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Journal d'activité (CDC §54) : lecture seule, aucune création, modification
 * ni suppression possible.
 */
class JournalActiviteResource extends Resource
{
    protected static ?string $model = JournalActivite::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Paramètres';

    protected static ?int $navigationSort = 20;

    protected static ?string $modelLabel = 'ligne du journal';

    protected static ?string $pluralModelLabel = "journal d'activité";

    protected static ?string $navigationLabel = "Journal d'activité";

    protected static ?string $slug = 'journal';

    public static function infolist(Schema $schema): Schema
    {
        return JournalActiviteInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JournalActivitesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJournalActivites::route('/'),
            'view' => ViewJournalActivite::route('/{record}'),
        ];
    }
}
