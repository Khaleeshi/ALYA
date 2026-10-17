<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Cliente>
 */
class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    protected static ?string $motDePasse;

    public function definition(): array
    {
        return [
            'prenom' => fake()->firstNameFemale(),
            'nom' => fake()->lastName(),
            'telephone' => '+22177'.fake()->unique()->numerify('#######'),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$motDePasse ??= Hash::make('mot-de-passe'),
            'langue' => 'fr',
        ];
    }

    public function anglophone(): static
    {
        return $this->state(['langue' => 'en']);
    }
}
