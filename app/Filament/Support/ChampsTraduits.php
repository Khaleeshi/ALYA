<?php

namespace App\Filament\Support;

use App\Support\Langues;
use Closure;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

/**
 * Champs des textes traduisibles du catalogue. Chaque champ s'écrit
 * « nom.fr », « nom.en » : Filament lit et rend la valeur de chaque langue
 * telle que le modèle la stocke (spatie/laravel-translatable).
 *
 * Tant que seul le français est ouvert, les champs s'affichent normalement ;
 * dès qu'une autre langue s'ouvre, ils se rangent dans un onglet par langue.
 * Le français est obligatoire là où le champ l'est ; une langue laissée vide
 * retombe sur le français.
 */
final class ChampsTraduits
{
    private const NOMS = ['fr' => 'Français', 'en' => 'English'];

    /**
     * @param  Closure(string $langue, bool $principale): array<Component>  $champs
     */
    public static function pour(Closure $champs): Component
    {
        $langues = Langues::ouvertes();

        if (count($langues) === 1) {
            return Group::make($champs($langues[0], true));
        }

        return Tabs::make('Langues')
            ->tabs(array_map(
                fn (string $langue): Tab => Tab::make(self::NOMS[$langue] ?? mb_strtoupper($langue))
                    ->schema($champs($langue, $langue === Langues::PAR_DEFAUT)),
                $langues,
            ));
    }
}
