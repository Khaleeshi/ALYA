<?php

namespace Database\Seeders;

use App\Models\Categorie;
use Illuminate\Database\Seeder;

/**
 * Les trois catégories du lancement (cahier §1.1). Seuls les noms viennent
 * du cahier : descriptions et photos se saisissent dans l'administration.
 * Peut être relancé : une catégorie existante n'est pas touchée.
 */
class CategoriesSeeder extends Seeder
{
    public const CATEGORIES = [
        'gloss' => 'Gloss',
        'skincare' => 'Skincare',
        'maquillage' => 'Maquillage',
    ];

    public function run(): void
    {
        $position = 0;

        foreach (self::CATEGORIES as $slug => $nom) {
            Categorie::query()->firstOrCreate(['slug' => $slug], ['nom' => ['fr' => $nom], 'position' => $position++]);
        }
    }
}
