<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\Raccourcis;
use Filament\Pages\Dashboard;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Accueil de l'administration (maquette 6) : « Bienvenue Prénom ! ».
 * Les chiffres (ventes, commandes, clients, stock faible) apparaîtront avec
 * les commandes ; aucun chiffre n'est affiché avant d'exister.
 */
class TableauDeBord extends Dashboard
{
    protected static ?string $title = 'Tableau de bord';

    public function getHeading(): string|Htmlable
    {
        return 'Bienvenue '.auth('personnel')->user()?->prenom.' !';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Voici un aperçu de votre boutique AMANI Cosmetics.';
    }

    public function getWidgets(): array
    {
        return [
            Raccourcis::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 1;
    }
}
