<?php

use App\Http\Middleware\DefinirLangue;
use App\Support\Langues;
use Illuminate\Support\Arr;

it("envoie l'adresse nue vers le français", function () {
    $this->get('/')->assertRedirect('/fr');
});

it("garde l'anglais fermé au lancement (cahier §1.2)", function () {
    expect(Langues::ouvertes())->toBe(['fr'])
        ->and(Langues::plusieurs())->toBeFalse();

    $this->get('/en')->assertNotFound();
    $this->get('/en/login')->assertNotFound();
});

it("ignore une langue retenue qui n'est pas ouverte", function () {
    $this->withUnencryptedCookie(DefinirLangue::COOKIE, 'en')
        ->get('/')
        ->assertRedirect('/fr');
});

it("n'affiche ni sélecteur de langue ni balises hreflang avec une seule langue", function () {
    $this->get('/fr')
        ->assertDontSee('hreflang', false)
        ->assertDontSee('English');
});

it("ouvre l'anglais par un simple réglage", function () {
    config(['boutique.langues' => ['fr', 'en']]);

    expect(Langues::ouvertes())->toBe(['fr', 'en'])
        ->and(Langues::chemin('connexion', 'en'))->toBe('login');
});

it('garde une traduction anglaise prête pour chaque texte français', function (string $fichier) {
    $francais = array_keys(Arr::dot(require lang_path("fr/{$fichier}.php")));
    $anglais = array_keys(Arr::dot(require lang_path("en/{$fichier}.php")));

    expect(array_values(array_diff($francais, $anglais)))->toBe([], "Clés absentes en anglais dans {$fichier}.php")
        ->and(array_values(array_diff($anglais, $francais)))->toBe([], "Clés absentes en français dans {$fichier}.php");
})->with(['vitrine', 'comptes', 'chemins']);
