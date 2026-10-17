<?php

use App\Models\Cliente;
use App\Models\JournalActivite;
use App\Support\Securite\VerrouillageConnexion;

beforeEach(function () {
    $this->freezeTime();

    $this->cliente = Cliente::factory()->create([
        'telephone' => '+221771234567',
        'email' => 'awa@example.com',
        'password' => 'brillance2026',
    ]);
});

function seConnecter(string $identifiant, string $motDePasse, string $langue = 'fr')
{
    $chemin = $langue === 'fr' ? '/fr/connexion' : '/en/login';

    return test()->from($chemin)->post($chemin, ['identifiant' => $identifiant, 'password' => $motDePasse]);
}

it('connecte une cliente par son email', function () {
    seConnecter('Awa@Example.com', 'brillance2026')->assertRedirect('/fr/mon-compte');

    $this->assertAuthenticatedAs($this->cliente, 'web');
});

it('connecte une cliente par son téléphone, écrit comme elle veut', function (string $telephone) {
    seConnecter($telephone, 'brillance2026')->assertRedirect('/fr/mon-compte');

    $this->assertAuthenticatedAs($this->cliente, 'web');
})->with(['77 123 45 67', '+221771234567', '00221 77 123 45 67']);

it('refuse un mauvais mot de passe sans dire si le compte existe', function () {
    seConnecter('awa@example.com', 'mauvais')->assertSessionHasErrors(['identifiant' => __('auth.failed')]);
    seConnecter('inconnue@example.com', 'mauvais')->assertSessionHasErrors(['identifiant' => __('auth.failed')]);

    $this->assertGuest('web');
});

it('verrouille la connexion après cinq échecs, même avec le bon mot de passe ensuite', function () {
    foreach (range(1, VerrouillageConnexion::ESSAIS) as $essai) {
        seConnecter('awa@example.com', 'mauvais-'.$essai);
    }

    seConnecter('awa@example.com', 'brillance2026')
        ->assertSessionHasErrors(['identifiant' => __('auth.throttle', ['seconds' => 900, 'minutes' => 15])]);

    $this->assertGuest('web');

    expect(JournalActivite::where('action', 'connexion.verrouillage')->sole()->donnees)
        ->toMatchArray(['espace' => 'boutique', 'identifiant' => 'awa@example.com']);
});

it('compte les échecs par téléphone quelle que soit son écriture', function () {
    $ecritures = ['77 123 45 67', '771234567', '+221771234567', '00221771234567', '221 77 123 45 67'];

    foreach ($ecritures as $telephone) {
        seConnecter($telephone, 'mauvais');
    }

    seConnecter('77 123 45 67', 'brillance2026');

    $this->assertGuest('web');
});

it('déverrouille au bout de quinze minutes', function () {
    foreach (range(1, VerrouillageConnexion::ESSAIS) as $essai) {
        seConnecter('awa@example.com', 'mauvais');
    }

    $this->travel(VerrouillageConnexion::DUREE_SECONDES + 1)->seconds();

    seConnecter('awa@example.com', 'brillance2026')->assertRedirect('/fr/mon-compte');
    $this->assertAuthenticatedAs($this->cliente, 'web');
});

it('remet le compteur à zéro après une connexion réussie', function () {
    foreach (range(1, VerrouillageConnexion::ESSAIS - 1) as $essai) {
        seConnecter('awa@example.com', 'mauvais');
    }

    seConnecter('awa@example.com', 'brillance2026')->assertRedirect('/fr/mon-compte');
    $this->post('/fr/deconnexion');

    foreach (range(1, VerrouillageConnexion::ESSAIS - 1) as $essai) {
        seConnecter('awa@example.com', 'mauvais');
    }

    seConnecter('awa@example.com', 'brillance2026')->assertRedirect('/fr/mon-compte');
});

it('affiche le message de verrouillage en anglais sur le site anglais', function () {
    foreach (range(1, VerrouillageConnexion::ESSAIS) as $essai) {
        seConnecter('awa@example.com', 'mauvais', 'en');
    }

    seConnecter('awa@example.com', 'brillance2026', 'en')
        ->assertSessionHasErrors(['identifiant' => trans('auth.throttle', ['seconds' => 900, 'minutes' => 15], 'en')]);
});

it('déconnecte la cliente et la ramène à l\'accueil', function () {
    $this->actingAs($this->cliente, 'web')
        ->post('/fr/deconnexion')
        ->assertRedirect('/fr');

    $this->assertGuest('web');
});

it('envoie une visiteuse non connectée vers la connexion, dans sa langue', function () {
    $this->get('/fr/mon-compte')->assertRedirect('/fr/connexion');
    $this->get('/en/my-account')->assertRedirect('/en/login');
});

it("affiche l'espace cliente avec ses coordonnées", function () {
    $this->actingAs($this->cliente, 'web')
        ->get('/fr/mon-compte')
        ->assertOk()
        ->assertSee('+221 77 123 45 67')
        ->assertSee('awa@example.com');
});
