<?php

use App\Models\Produit;
use App\Models\Utilisateur;
use App\Support\Acces\Roles;
use Database\Seeders\RecetteSeeder;

it('remplit la version d’essai et crée le compte de la propriétaire une seule fois', function () {
    config(['boutique.recette.proprietaire' => ['email' => 'Nogaye@Amani.test', 'mot_de_passe' => 'EssaiAmani2026', 'prenom' => 'Nogaye']]);

    $this->seed(RecetteSeeder::class);
    $this->seed(RecetteSeeder::class);

    $proprietaire = Utilisateur::sole();

    expect($proprietaire->email)->toBe('nogaye@amani.test')
        ->and($proprietaire->prenom)->toBe('Nogaye')
        ->and($proprietaire->hasRole(Roles::PROPRIETAIRE))->toBeTrue()
        ->and(Hash::check('EssaiAmani2026', $proprietaire->password))->toBeTrue()
        ->and(Produit::visibles()->count())->toBe(10);
});

it('ne crée pas de compte sans adresse ni mot de passe réglés', function () {
    config(['boutique.recette.proprietaire' => ['email' => null, 'mot_de_passe' => null, 'prenom' => 'Propriétaire']]);

    $this->seed(RecetteSeeder::class);

    expect(Utilisateur::count())->toBe(0);
});

it('refuse de remplir la production', function () {
    app()->detectEnvironment(fn () => 'production');

    expect(fn () => app(RecetteSeeder::class)->run())->toThrow(RuntimeException::class);
    $this->artisan('amani:preparer-recette')->assertFailed();
});

it('affiche le code de connexion à l’écran en recette quand c’est demandé', function () {
    config(['services.sms.afficher_en_recette' => true]);

    $this->post('/fr/connexion', ['telephone' => '77 123 45 67'])->assertRedirect();

    $this->get('/fr/connexion/code')
        ->assertOk()
        ->assertSee(__('comptes.code.recette'))
        ->assertSee('votre code de connexion est', false);
});

it('n’affiche jamais le code par défaut', function () {
    $this->post('/fr/connexion', ['telephone' => '77 123 45 67']);

    $this->get('/fr/connexion/code')
        ->assertOk()
        ->assertDontSee(__('comptes.code.recette'));
});
