<?php

use App\Filament\Pages\Auth\Connexion;
use App\Models\Cliente;
use App\Models\JournalActivite;
use App\Models\Utilisateur;
use App\Support\Securite\VerrouillageConnexion;
use Filament\Facades\Filament;

beforeEach(function () {
    Filament::setCurrentPanel('administration');
    $this->freezeTime();
    $this->secret = secretDoubleAuthentification();
});

function connexionAdministration(string $email, string $motDePasse)
{
    return livewire(Connexion::class)
        ->fillForm(['email' => $email, 'password' => $motDePasse])
        ->call('authenticate');
}

it('demande le code de double authentification après un mot de passe juste', function () {
    $proprietaire = proprietaire(['email' => 'pdg@amani.test', 'app_authentication_secret' => $this->secret]);

    connexionAdministration('pdg@amani.test', 'mot-de-passe-du-personnel')
        ->assertHasNoFormErrors()
        ->assertNoRedirect();

    $this->assertGuest('personnel');
});

it('ouvre l\'administration avec le bon code de double authentification', function () {
    $proprietaire = proprietaire(['email' => 'pdg@amani.test', 'app_authentication_secret' => $this->secret]);

    connexionAdministration('pdg@amani.test', 'mot-de-passe-du-personnel')
        ->set('data.multiFactor.app.code', codeDoubleAuthentification($this->secret))
        ->call('authenticate')
        ->assertRedirect();

    $this->assertAuthenticatedAs($proprietaire, 'personnel');

    expect(JournalActivite::where('action', 'personnel.connexion')->sole()->auteur_libelle)->toBe('Propriétaire · '.$proprietaire->prenom)
        ->and($proprietaire->fresh()->derniere_connexion_le)->not->toBeNull();
});

it('refuse un mauvais code de double authentification', function () {
    proprietaire(['email' => 'pdg@amani.test', 'app_authentication_secret' => $this->secret]);

    connexionAdministration('pdg@amani.test', 'mot-de-passe-du-personnel')
        ->set('data.multiFactor.app.code', '000000')
        ->call('authenticate')
        ->assertHasFormErrors();

    $this->assertGuest('personnel');
});

it('impose de configurer la double authentification à un compte qui ne l\'a pas encore', function () {
    administrateur(['email' => 'admin@amani.test']);

    connexionAdministration('admin@amani.test', 'mot-de-passe-du-personnel')->assertRedirect();

    $this->actingAs(Utilisateur::firstWhere('email', 'admin@amani.test'), 'personnel')
        ->get('/administration')
        ->assertRedirect();
});

it('verrouille la connexion après cinq échecs, même avec le bon mot de passe ensuite', function () {
    proprietaire(['email' => 'pdg@amani.test', 'app_authentication_secret' => $this->secret]);

    foreach (range(1, VerrouillageConnexion::ESSAIS) as $essai) {
        connexionAdministration('pdg@amani.test', 'mauvais-'.$essai)->assertHasFormErrors();
    }

    connexionAdministration('pdg@amani.test', 'mot-de-passe-du-personnel')
        ->assertNotified(__('auth.throttle', ['seconds' => 900, 'minutes' => 15]));

    expect(JournalActivite::where('action', 'connexion.verrouillage')->sole()->donnees)
        ->toMatchArray(['espace' => 'administration', 'identifiant' => 'pdg@amani.test']);
});

it('refuse les comptes désactivés et les livreurs', function () {
    Utilisateur::factory()->proprietaire()->inactif()->create(['email' => 'ancienne@amani.test']);
    Utilisateur::factory()->livreur()->create(['email' => 'livreur@amani.test']);

    connexionAdministration('ancienne@amani.test', 'mot-de-passe-du-personnel')->assertHasFormErrors(['email']);
    connexionAdministration('livreur@amani.test', 'mot-de-passe-du-personnel')->assertHasFormErrors(['email']);

    $this->assertGuest('personnel');
});

it("n'ouvre jamais l'administration à une cliente connectée à la boutique", function () {
    $cliente = Cliente::factory()->create();

    $this->actingAs($cliente, 'web')
        ->get('/administration')
        ->assertRedirect('/administration/login');
});
