<?php

use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

pest()->extend(TestCase::class)->in('Unit');

/**
 * Code de double authentification valable maintenant pour ce secret,
 * comme l'afficherait l'application du téléphone.
 */
function codeDoubleAuthentification(string $secret): string
{
    return app(Google2FA::class)->getCurrentOtp($secret);
}

function secretDoubleAuthentification(): string
{
    return app(Google2FA::class)->generateSecretKey();
}

function dirigeante(array $attributs = []): Utilisateur
{
    return Utilisateur::factory()->dirigeante()->create($attributs);
}

function administrateur(array $attributs = []): Utilisateur
{
    return Utilisateur::factory()->administrateur()->create($attributs);
}

/**
 * Écran Livewire (Filament) prêt à être testé.
 */
function livewire(string $composant, array $parametres = []): Testable
{
    return Livewire::test($composant, $parametres);
}
