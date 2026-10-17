<?php

namespace Database\Factories;

use App\Models\Utilisateur;
use App\Support\Acces\Roles;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Utilisateur>
 */
class UtilisateurFactory extends Factory
{
    protected $model = Utilisateur::class;

    protected static ?string $motDePasse;

    public function definition(): array
    {
        return [
            'prenom' => fake()->firstName(),
            'nom' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$motDePasse ??= Hash::make('mot-de-passe-du-personnel'),
            'actif' => true,
        ];
    }

    public function dirigeante(): static
    {
        return $this->afterCreating(fn (Utilisateur $utilisateur) => $utilisateur->assignRole(Roles::DIRIGEANTE));
    }

    public function administrateur(): static
    {
        return $this->afterCreating(fn (Utilisateur $utilisateur) => $utilisateur->assignRole(Roles::ADMINISTRATEUR));
    }

    public function livreur(): static
    {
        return $this->afterCreating(fn (Utilisateur $utilisateur) => $utilisateur->assignRole(Roles::LIVREUR));
    }

    public function inactif(): static
    {
        return $this->state(['actif' => false]);
    }

    /**
     * Double authentification déjà configurée avec le secret donné.
     */
    public function avecDoubleAuthentification(string $secret): static
    {
        return $this->state(['app_authentication_secret' => $secret]);
    }
}
