<?php

namespace Database\Factories;

use App\Models\Gamme;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Gamme>
 */
class GammeFactory extends Factory
{
    protected $model = Gamme::class;

    public function definition(): array
    {
        return [
            'nom' => ['fr' => 'Collection '.ucfirst(fake()->unique()->word())],
            'type' => Gamme::PERMANENTE,
            'accroche' => ['fr' => fake()->sentence(5)],
            'en_ligne' => true,
        ];
    }

    public function editionLimitee(): static
    {
        return $this->state(['type' => Gamme::EDITION_LIMITEE]);
    }
}
