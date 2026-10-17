<?php

use App\Http\Middleware\DefinirLangue;
use App\Models\Cliente;
use Illuminate\Support\Arr;

it('envoie l\'adresse nue vers le français par défaut', function () {
    $this->get('/')->assertRedirect('/fr');
});

it('se souvient de la langue choisie', function () {
    $this->withUnencryptedCookie(DefinirLangue::COOKIE, 'en')
        ->get('/')
        ->assertRedirect('/en');
});

it('retient la langue sur le compte de la cliente connectée', function () {
    $cliente = Cliente::factory()->create(['langue' => 'fr']);

    $this->actingAs($cliente, 'web')->get('/en');

    expect($cliente->fresh()->langue)->toBe('en');
});

it('affiche chaque page dans la langue de son adresse, avec ses équivalents', function () {
    $this->get('/en/login')
        ->assertOk()
        ->assertSee('<html lang="en">', false)
        ->assertSee('Sign in')
        ->assertSee('hreflang="fr" href="'.url('/fr/connexion').'"', false)
        ->assertDontSee('Mot de passe');

    $this->get('/fr/connexion')
        ->assertOk()
        ->assertSee('<html lang="fr">', false)
        ->assertSee('Mot de passe')
        ->assertSee('hreflang="en" href="'.url('/en/login').'"', false);
});

it('a une traduction anglaise pour chaque texte français de la vitrine', function (string $fichier) {
    $francais = array_keys(Arr::dot(require lang_path("fr/{$fichier}.php")));
    $anglais = array_keys(Arr::dot(require lang_path("en/{$fichier}.php")));

    expect(array_values(array_diff($francais, $anglais)))->toBe([], "Clés absentes en anglais dans {$fichier}.php")
        ->and(array_values(array_diff($anglais, $francais)))->toBe([], "Clés absentes en français dans {$fichier}.php");
})->with(['vitrine', 'comptes', 'chemins']);
