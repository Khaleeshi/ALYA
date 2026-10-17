<?php

namespace App\Filament\Resources\Utilisateurs\Tables;

use App\Models\Utilisateur;
use App\Support\Acces\Roles;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UtilisateursTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('nom')
            ->columns([
                TextColumn::make('nom_complet')
                    ->label('Nom')
                    ->searchable(['prenom', 'nom'])
                    ->sortable(['nom', 'prenom']),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Rôle')
                    ->formatStateUsing(fn (string $state): string => Roles::libelles()[$state] ?? $state)
                    ->badge()
                    ->color('gray'),
                IconColumn::make('actif')
                    ->label('Actif')
                    ->boolean(),
                IconColumn::make('double_authentification')
                    ->label('Double authentification')
                    ->state(fn (Utilisateur $record): bool => filled($record->app_authentication_secret))
                    ->boolean(),
                TextColumn::make('derniere_connexion_le')
                    ->label('Dernière connexion')
                    ->dateTime()
                    ->placeholder('Jamais')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Rôle')
                    ->options(Roles::libelles())
                    ->query(fn ($query, array $data) => filled($data['value'])
                        ? $query->role($data['value'])
                        : $query),
                TernaryFilter::make('actif')
                    ->label('Compte actif'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
