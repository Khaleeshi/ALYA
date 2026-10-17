<?php

use App\Models\Cliente;
use App\Models\Consentement;
use App\Models\JournalActivite;

beforeEach(function () {
    $this->cliente = Cliente::factory()->sansEmail()->create([
        'prenom' => 'Princesse',
        'nom' => 'Ba',
        'telephone' => '+221771234567',
    ]);
});

it('accueille la cliente dans son espace avec son menu et son profil', function () {
    $this->actingAs($this->cliente, 'web')
        ->get('/fr/mon-compte')
        ->assertOk()
        ->assertSee('Bienvenue')
        ->assertSee('Princesse')
        ->assertSee('Membre depuis le')
        ->assertSee(__('comptes.espace.menu.tableau'))
        ->assertSee(__('comptes.espace.menu.profil'))
        ->assertSee('+221 77 123 45 67');
});

it("n'affiche dans le menu que les rubriques déjà construites", function () {
    $this->actingAs($this->cliente, 'web')
        ->get('/fr/mon-compte')
        ->assertDontSee(__('comptes.espace.menu.commandes'))
        ->assertDontSee(__('comptes.espace.menu.points'));
});

it('enregistre le profil, met l\'email en minuscules et le note au journal', function () {
    $this->actingAs($this->cliente, 'web')
        ->put('/fr/mon-compte/profil', ['prenom' => 'Princesse', 'nom' => 'Ba Diop', 'email' => 'Princesse@Example.com'])
        ->assertRedirect('/fr/mon-compte/profil')
        ->assertSessionHas('statut', __('comptes.profil.enregistre'));

    $cliente = $this->cliente->fresh();

    expect($cliente->nom)->toBe('Ba Diop')
        ->and($cliente->email)->toBe('princesse@example.com')
        ->and($cliente->telephone)->toBe('+221771234567')
        ->and(JournalActivite::where('action', 'cliente.profil')->sole()->description)->toContain('nom')->toContain('email');
});

it("enregistre un changement d'accord pour les offres avec son origine", function () {
    $this->actingAs($this->cliente, 'web')
        ->put('/fr/mon-compte/profil', ['prenom' => 'Princesse', 'nom' => 'Ba', 'offres' => '1']);

    $consentement = Consentement::sole();

    expect($consentement->accorde)->toBeTrue()
        ->and($consentement->origine)->toBe(Consentement::ORIGINE_PROFIL);
});

it("ne touche pas au journal quand rien n'a changé", function () {
    $this->actingAs($this->cliente, 'web')
        ->put('/fr/mon-compte/profil', ['prenom' => 'Princesse', 'nom' => 'Ba']);

    expect(JournalActivite::where('action', 'cliente.profil')->exists())->toBeFalse();
});

it("refuse l'email d'une autre cliente", function () {
    Cliente::factory()->create(['email' => 'prise@example.com']);

    $this->actingAs($this->cliente, 'web')
        ->put('/fr/mon-compte/profil', ['prenom' => 'Princesse', 'nom' => 'Ba', 'email' => 'Prise@Example.com'])
        ->assertSessionHasErrors('email');
});

it('ne laisse pas changer le numéro de téléphone depuis le profil', function () {
    $this->actingAs($this->cliente, 'web')
        ->put('/fr/mon-compte/profil', ['prenom' => 'Princesse', 'nom' => 'Ba', 'telephone' => '+221780000000']);

    expect($this->cliente->fresh()->telephone)->toBe('+221771234567');
});
