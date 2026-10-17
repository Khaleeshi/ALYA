<?php

it('accueille la propriétaire par son prénom avec les actions rapides', function () {
    $this->actingAs(proprietaire(['prenom' => 'Princesse', 'app_authentication_secret' => secretDoubleAuthentification()]), 'personnel')
        ->get('/administration')
        ->assertOk()
        ->assertSee('Bienvenue Princesse !')
        ->assertSee('Voici un aperçu de votre boutique AMANI Cosmetics.')
        ->assertSee('Ajouter un membre du personnel')
        ->assertSee("Consulter le journal d'activité")
        ->assertSee('Voir ma boutique');
});

it("ne propose à l'administrateur que les actions qu'il a le droit de faire", function () {
    $this->actingAs(administrateur(['app_authentication_secret' => secretDoubleAuthentification()]), 'personnel')
        ->get('/administration')
        ->assertOk()
        ->assertDontSee('Ajouter un membre du personnel')
        ->assertSee('Voir ma boutique');
});
