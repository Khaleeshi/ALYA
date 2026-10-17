<?php

namespace App\Filament\Resources\JournalActivites\Tables;

use App\Models\JournalActivite;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class JournalActivitesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('cree_le', 'desc')
            ->columns([
                TextColumn::make('cree_le')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                TextColumn::make('auteur_libelle')
                    ->label('Auteur')
                    ->searchable(),
                TextColumn::make('action')
                    ->label('Action')
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('adresse_ip')
                    ->label('Adresse IP')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('auteur_type')
                    ->label('Auteur')
                    ->options([
                        JournalActivite::AUTEUR_PERSONNEL => 'Personnel',
                        JournalActivite::AUTEUR_CLIENTE => 'Clientes',
                        JournalActivite::AUTEUR_SYSTEME => 'Système',
                    ]),
                SelectFilter::make('action')
                    ->label('Action')
                    ->options(fn (): array => JournalActivite::query()->distinct()->orderBy('action')->pluck('action', 'action')->all()),
                Filter::make('periode')
                    ->label('Période')
                    ->schema([
                        DatePicker::make('du')->label('Du'),
                        DatePicker::make('au')->label('Au'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['du'] ?? null, fn (Builder $q, string $date) => $q->whereDate('cree_le', '>=', $date))
                        ->when($data['au'] ?? null, fn (Builder $q, string $date) => $q->whereDate('cree_le', '<=', $date))),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
