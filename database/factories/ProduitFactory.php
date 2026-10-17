<?php

namespace Database\Factories;

use App\Models\Categorie;
use App\Models\Produit;
use App\Models\Variante;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Produit publié avec une variante sans nom (10 en stock), sauf indication contraire.
 *
 * @extends Factory<Produit>
 */
class ProduitFactory extends Factory
{
    protected $model = Produit::class;

    public function definition(): array
    {
        return [
            'categorie_id' => Categorie::factory(),
            'nom' => ['fr' => ucfirst(fake()->unique()->words(3, true))],
            'accroche' => ['fr' => fake()->sentence(8)],
            'description' => ['fr' => fake()->paragraph()],
            'prix' => fake()->numberBetween(3, 30) * 500,
            'statut' => Produit::PUBLIE,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Produit $produit): void {
            if (! $produit->variantes()->exists()) {
                Variante::factory()->for($produit)->create();
            }
        });
    }

    public function brouillon(): static
    {
        return $this->state(['statut' => Produit::BROUILLON]);
    }

    public function editionLimitee(): static
    {
        return $this->state(['edition_limitee' => true]);
    }

    public function coffret(): static
    {
        return $this->state(['nature' => Produit::NATURE_COFFRET]);
    }

    /**
     * Variantes nommées : ['Nude Glow' => 12, 'Rose Diamant' => 0]
     *
     * @param  array<string, int>  $stocks
     */
    public function avecTeintes(array $stocks): static
    {
        $etats = [];

        foreach ($stocks as $nom => $stock) {
            $etats[] = ['nom' => ['fr' => $nom], 'couleur' => fake()->hexColor(), 'stock' => $stock, 'position' => count($etats)];
        }

        // Les variantes de has() sont créées avant configure() : pas de variante sans nom en plus.
        return $this->has(Variante::factory()->count(count($etats))->sequence(...$etats), 'variantes');
    }
}
