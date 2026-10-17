<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cliente>
 */
class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array
    {
        return [
            'prenom' => fake()->firstNameFemale(),
            'nom' => fake()->lastName(),
            'telephone' => '+22177'.fake()->unique()->numerify('#######'),
            'telephone_verifie_le' => now(),
            'email' => fake()->unique()->safeEmail(),
            'langue' => 'fr',
        ];
    }

    public function sansEmail(): static
    {
        return $this->state(['email' => null]);
    }
}
