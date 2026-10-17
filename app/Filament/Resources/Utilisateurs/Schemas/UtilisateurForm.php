<?php

namespace App\Filament\Resources\Utilisateurs\Schemas;

use App\Models\Utilisateur;
use App\Rules\TelephoneValide;
use App\Support\Acces\Permissions;
use App\Support\Acces\Roles;
use App\Support\Telephone;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

class UtilisateurForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Identité')
                    ->columns(2)
                    ->schema([
                        TextInput::make('prenom')
                            ->label('Prénom')
                            ->required()
                            ->maxLength(80),
                        TextInput::make('nom')
                            ->label('Nom')
                            ->required()
                            ->maxLength(80),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('telephone')
                            ->label('Téléphone')
                            ->tel()
                            ->rule(new TelephoneValide)
                            ->dehydrateStateUsing(fn (?string $state): ?string => Telephone::normaliser($state) ?? $state),
                    ]),

                Section::make('Accès')
                    ->description('Le rôle donne un ensemble de permissions. La double authentification sera demandée à la première connexion.')
                    ->schema([
                        Radio::make('role')
                            ->label('Rôle')
                            ->options(Roles::libelles())
                            ->descriptions([
                                Roles::PROPRIETAIRE => 'Voit et peut tout, y compris les achats et les remboursements.',
                                Roles::ADMINISTRATEUR => 'Gestion courante : catalogue, commandes, livraisons, après-vente, contenus, bilans, journal.',
                                Roles::LIVREUR => "Espace livreur uniquement, sans accès à l'administration.",
                            ])
                            ->required()
                            ->live()
                            ->dehydrated(false)
                            ->disabled(fn (?Utilisateur $record): bool => $record?->is(auth('personnel')->user()) ?? false)
                            ->helperText(fn (?Utilisateur $record): ?string => $record?->is(auth('personnel')->user())
                                ? 'Vous ne pouvez pas changer votre propre rôle.'
                                : null),

                        CheckboxList::make('permissions_supplementaires')
                            ->label('Permissions en plus du rôle')
                            ->options(fn (Get $get): array => array_diff_key(
                                Permissions::libelles(),
                                array_flip(Permissions::PAR_ROLE[$get('role')] ?? []),
                            ))
                            ->columns(2)
                            ->dehydrated(false)
                            ->hidden(fn (Get $get): bool => in_array($get('role'), [null, Roles::PROPRIETAIRE], true)),

                        Toggle::make('actif')
                            ->label('Compte actif')
                            ->helperText('Un compte désactivé ne peut plus se connecter ; son historique reste au journal.')
                            ->default(true)
                            ->disabled(fn (?Utilisateur $record): bool => $record?->is(auth('personnel')->user()) ?? false),
                    ]),

                Section::make('Mot de passe')
                    ->schema([
                        TextInput::make('password')
                            ->label(fn (string $operation): string => $operation === 'create' ? 'Mot de passe' : 'Nouveau mot de passe')
                            ->helperText(fn (string $operation): string => $operation === 'create'
                                ? 'Au moins 12 caractères. À transmettre en main propre.'
                                : 'Laisser vide pour ne pas le changer.')
                            ->password()
                            ->revealable()
                            ->rule(Password::min(12)->letters()->numbers())
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state)),
                    ]),
            ]);
    }
}
