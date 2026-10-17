<?php

namespace Database\Factories;

use App\Models\Effet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Effet>
 */
class EffetFactory extends Factory
{
    protected $model = Effet::class;

    public function definition(): array
    {
        return [
            'nom' => ['fr' => ucfirst(fake()->unique()->word())],
        ];
    }
}
