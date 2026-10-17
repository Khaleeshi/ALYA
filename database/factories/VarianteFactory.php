<?php

namespace Database\Factories;

use App\Models\Produit;
use App\Models\Variante;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Variante>
 */
class VarianteFactory extends Factory
{
    protected $model = Variante::class;

    public function definition(): array
    {
        return [
            'produit_id' => Produit::factory(),
            'stock' => 10,
            'active' => true,
        ];
    }
}
