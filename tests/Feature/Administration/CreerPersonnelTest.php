<?php

use App\Models\JournalActivite;
use App\Models\Utilisateur;
use App\Support\Acces\Roles;

it('crée le premier compte de la propriétaire depuis le serveur', function () {
    $this->artisan('amani:creer-personnel')
        ->expectsQuestion('Prénom', 'Ndeye')
        ->expectsQuestion('Nom', 'Fall')
        ->expectsQuestion('Email', 'pdg@amani.test')
        ->expectsQuestion('Rôle', Roles::PROPRIETAIRE)
        ->expectsQuestion('Mot de passe (12 caractères au moins, lettres et chiffres)', 'BrillanceDakar2026')
        ->assertSuccessful();

    $proprietaire = Utilisateur::firstWhere('email', 'pdg@amani.test');

    expect($proprietaire->estProprietaire())->toBeTrue()
        ->and(JournalActivite::where('action', 'personnel.creation')->exists())->toBeTrue();
});

it('refuse un mot de passe trop court', function () {
    $this->artisan('amani:creer-personnel')
        ->expectsQuestion('Prénom', 'Ndeye')
        ->expectsQuestion('Nom', 'Fall')
        ->expectsQuestion('Email', 'pdg@amani.test')
        ->expectsQuestion('Rôle', Roles::PROPRIETAIRE)
        ->expectsQuestion('Mot de passe (12 caractères au moins, lettres et chiffres)', 'court')
        ->assertFailed();

    expect(Utilisateur::count())->toBe(0);
});
