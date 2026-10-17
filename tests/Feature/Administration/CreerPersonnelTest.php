<?php

use App\Models\JournalActivite;
use App\Models\Utilisateur;
use App\Support\Acces\Roles;

it('crée le premier compte de la dirigeante depuis le serveur', function () {
    $this->artisan('alya:creer-personnel')
        ->expectsQuestion('Prénom', 'Ndeye')
        ->expectsQuestion('Nom', 'Fall')
        ->expectsQuestion('Email', 'pdg@alyacosmetic.sn')
        ->expectsQuestion('Rôle', Roles::DIRIGEANTE)
        ->expectsQuestion('Mot de passe (12 caractères au moins, lettres et chiffres)', 'BrillanceDakar2026')
        ->assertSuccessful();

    $dirigeante = Utilisateur::firstWhere('email', 'pdg@alyacosmetic.sn');

    expect($dirigeante->estDirigeante())->toBeTrue()
        ->and(JournalActivite::where('action', 'personnel.creation')->exists())->toBeTrue();
});

it('refuse un mot de passe trop court', function () {
    $this->artisan('alya:creer-personnel')
        ->expectsQuestion('Prénom', 'Ndeye')
        ->expectsQuestion('Nom', 'Fall')
        ->expectsQuestion('Email', 'pdg@alyacosmetic.sn')
        ->expectsQuestion('Rôle', Roles::DIRIGEANTE)
        ->expectsQuestion('Mot de passe (12 caractères au moins, lettres et chiffres)', 'court')
        ->assertFailed();

    expect(Utilisateur::count())->toBe(0);
});
