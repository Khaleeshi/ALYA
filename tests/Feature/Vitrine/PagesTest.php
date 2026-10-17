<?php

use Illuminate\View\ViewException;

it("affiche l'accueil avec la signature AMANI", function () {
    $this->get('/fr')
        ->assertOk()
        ->assertSee('<html lang="fr">', false)
        ->assertSee('AMANI')
        ->assertSee('Parce que vous méritez tous')
        ->assertSee('de briller.');
});

it('affiche le bandeau de service et la bande de réassurance', function () {
    $this->get('/fr')
        ->assertSee('Livraison partout au Sénégal')
        ->assertSee('Paiement sécurisé (Wave &amp; Orange Money)', false)
        ->assertSee('Cadeaux VIP');
});

it("n'affiche que les liens des pages qui existent déjà", function () {
    $this->get('/fr')
        ->assertSee('href="'.url('/fr').'"', false)
        ->assertDontSee('Guide beauté');
});

it("affiche l'écran de connexion par téléphone", function () {
    $this->get('/fr/connexion')
        ->assertOk()
        ->assertSee('+221')
        ->assertSee(__('comptes.connexion.envoyer'));
});

it('affiche la bibliothèque de composants hors production', function () {
    $this->get('/systeme-de-design')
        ->assertOk()
        ->assertSee('Système de design')
        ->assertSee('Ajouter au panier')
        ->assertSee('Guide beauté');
});

it('refuse un menu principal de plus de sept entrées', function () {
    $liens = array_fill(0, 8, ['libelle' => 'Entrée', 'url' => '#']);

    $this->blade('<x-amani.en-tete :liens="$liens" />', ['liens' => $liens]);
})->throws(ViewException::class);
