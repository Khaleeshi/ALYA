<?php

namespace App\Filament\Resources\JournalActivites\Schemas;

use App\Models\JournalActivite;
use Filament\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JournalActiviteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('cree_le')
                            ->label('Date')
                            ->dateTime('d/m/Y H:i:s'),
                        TextEntry::make('auteur_libelle')
                            ->label('Auteur'),
                        TextEntry::make('action')
                            ->label('Action')
                            ->badge()
                            ->color('gray'),
                        TextEntry::make('sujet')
                            ->label('Concerne')
                            ->state(fn (JournalActivite $record): ?string => $record->sujet_type ? $record->sujet_type.' n° '.$record->sujet_id : null)
                            ->placeholder('—'),
                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                        TextEntry::make('adresse_ip')
                            ->label('Adresse IP')
                            ->placeholder('—'),
                    ]),
                Section::make('Détails')
                    ->schema([
                        CodeEntry::make('donnees')
                            ->hiddenLabel()
                            ->state(fn (JournalActivite $record): ?string => $record->donnees === null
                                ? null
                                : json_encode($record->donnees, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)),
                    ])
                    ->visible(fn (JournalActivite $record): bool => $record->donnees !== null),
            ]);
    }
}
