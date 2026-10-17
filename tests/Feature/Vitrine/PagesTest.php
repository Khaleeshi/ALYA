<?php

use Illuminate\View\ViewException;

it('affiche l\'accueil avec la signature, en français et en anglais', function () {
    $this->get('/fr')->assertOk()->assertSee('Parce que vous méritez tous de briller.');
    $this->get('/en')->assertOk()->assertSee('Because you all deserve to shine.');
});

it('affiche les écrans de compte', function (string $adresse) {
    $this->get($adresse)->assertOk();
})->with([
    '/fr/connexion',
    '/fr/inscription',
    '/fr/mot-de-passe-oublie',
    '/en/login',
    '/en/register',
    '/en/forgot-password',
]);

it('affiche la bibliothèque de composants hors production', function () {
    $this->get('/systeme-de-design')
        ->assertOk()
        ->assertSee('Système de design ALYA')
        ->assertSee('Payer avec Wave');
});

it('refuse une navigation principale de plus de sept entrées', function () {
    $liens = array_fill(0, 8, ['libelle' => 'Entrée', 'url' => '#']);

    $this->blade('<x-alya.en-tete :liens="$liens" />', ['liens' => $liens]);
})->throws(ViewException::class);
