<?php

use App\Models\Cliente;
use App\Models\Consentement;
use App\Models\JournalActivite;

function donneesInscription(array $remplacements = []): array
{
    return array_merge([
        'prenom' => 'Awa',
        'nom' => 'Diop',
        'telephone' => '77 123 45 67',
        'email' => 'Awa.Diop@example.com',
        'password' => 'brillance2026',
        'password_confirmation' => 'brillance2026',
    ], $remplacements);
}

it('crée le compte, connecte la cliente et l\'envoie vers son espace', function () {
    $this->post('/fr/inscription', donneesInscription())
        ->assertRedirect('/fr/mon-compte');

    $cliente = Cliente::sole();

    expect($cliente->telephone)->toBe('+221771234567')
        ->and($cliente->email)->toBe('awa.diop@example.com')
        ->and($cliente->langue)->toBe('fr');

    $this->assertAuthenticatedAs($cliente, 'web');
});

it("n'inscrit jamais d'accord pour les offres sans case cochée, et garde la date et l'origine", function () {
    $this->post('/fr/inscription', donneesInscription());

    $consentement = Consentement::sole();

    expect($consentement->type)->toBe(Consentement::OFFRES)
        ->and($consentement->accorde)->toBeFalse()
        ->and($consentement->origine)->toBe(Consentement::ORIGINE_INSCRIPTION)
        ->and($consentement->cree_le)->not->toBeNull();
});

it("enregistre l'accord pour les offres quand la case est cochée", function () {
    $this->post('/fr/inscription', donneesInscription(['offres' => '1']));

    expect(Cliente::sole()->aConsenti(Consentement::OFFRES))->toBeTrue();
});

it('inscrit la création du compte au journal', function () {
    $this->post('/fr/inscription', donneesInscription());

    $ligne = JournalActivite::where('action', 'cliente.inscription')->sole();

    expect($ligne->auteur_type)->toBe(JournalActivite::AUTEUR_CLIENTE)
        ->and($ligne->auteur_libelle)->toBe('Cliente · Awa');
});

it('refuse un téléphone déjà utilisé, même écrit autrement', function () {
    Cliente::factory()->create(['telephone' => '+221771234567']);

    $this->from('/fr/inscription')
        ->post('/fr/inscription', donneesInscription(['telephone' => '00221 77 123 45 67']))
        ->assertRedirect('/fr/inscription')
        ->assertSessionHasErrors('telephone');

    expect(Cliente::count())->toBe(1);
});

it('refuse un numéro de téléphone impossible', function () {
    $this->post('/fr/inscription', donneesInscription(['telephone' => '77 12']))
        ->assertSessionHasErrors(['telephone' => __('validation.telephone')]);
});

it('retient la langue de la cliente inscrite en anglais', function () {
    $this->post('/en/register', donneesInscription())
        ->assertRedirect('/en/my-account');

    expect(Cliente::sole()->langue)->toBe('en');
});
