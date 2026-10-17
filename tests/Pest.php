<?php

use App\Models\Utilisateur;
use App\Support\Sms\EnvoiSms;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use PragmaRX\Google2FA\Google2FA;
use Tests\Fixtures\SmsFactice;
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

/**
 * Remplace l'envoi de SMS par une boîte en mémoire et la renvoie.
 */
function smsFactice(): SmsFactice
{
    $sms = new SmsFactice;
    app()->instance(EnvoiSms::class, $sms);

    return $sms;
}

function proprietaire(array $attributs = []): Utilisateur
{
    return Utilisateur::factory()->proprietaire()->create($attributs);
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
