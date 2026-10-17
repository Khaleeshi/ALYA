<?php

use App\Filament\Resources\Utilisateurs\Pages\CreateUtilisateur;
use App\Filament\Resources\Utilisateurs\Pages\EditUtilisateur;
use App\Models\JournalActivite;
use App\Models\Utilisateur;
use App\Support\Acces\Permissions;
use App\Support\Acces\Roles;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    Filament::setCurrentPanel('administration');
    $this->proprietaire = proprietaire(['prenom' => 'Ndeye', 'app_authentication_secret' => secretDoubleAuthentification()]);
    $this->actingAs($this->proprietaire, 'personnel');
});

it('crée un administrateur avec une permission en plus et le note au journal', function () {
    livewire(CreateUtilisateur::class)
        ->fillForm([
            'prenom' => 'Moussa',
            'nom' => 'Ba',
            'email' => 'Moussa@amani.test',
            'telephone' => '78 000 11 22',
            'role' => Roles::ADMINISTRATEUR,
            'permissions_supplementaires' => [Permissions::ACHATS_GERER],
            'actif' => true,
            'password' => 'MotDePasseSolide2026',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $moussa = Utilisateur::firstWhere('email', 'moussa@amani.test');

    expect($moussa->hasRole(Roles::ADMINISTRATEUR))->toBeTrue()
        ->and($moussa->telephone)->toBe('+221780001122')
        ->and($moussa->can(Permissions::ACHATS_GERER))->toBeTrue()
        ->and(Hash::check('MotDePasseSolide2026', $moussa->password))->toBeTrue();

    $ligne = JournalActivite::where('action', 'personnel.creation')->sole();
    expect($ligne->auteur_libelle)->toBe('Propriétaire · Ndeye')
        ->and($ligne->description)->toContain('Moussa Ba');
});

it('exige un mot de passe de douze caractères au moins', function () {
    livewire(CreateUtilisateur::class)
        ->fillForm([
            'prenom' => 'Moussa',
            'nom' => 'Ba',
            'email' => 'moussa@amani.test',
            'role' => Roles::ADMINISTRATEUR,
            'password' => 'court1',
        ])
        ->call('create')
        ->assertHasFormErrors(['password']);
});

it("désactive un compte et note qui l'a fait", function () {
    $administrateur = administrateur(['prenom' => 'Moussa', 'nom' => 'Ba']);

    livewire(EditUtilisateur::class, ['record' => $administrateur->getRouteKey()])
        ->assertSchemaStateSet(['role' => Roles::ADMINISTRATEUR])
        ->fillForm(['actif' => false])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($administrateur->fresh()->actif)->toBeFalse();

    $ligne = JournalActivite::where('action', 'personnel.modification')->sole();
    expect($ligne->description)->toContain('actif')
        ->and($ligne->donnees['avant']['actif'])->toBeTrue()
        ->and($ligne->donnees['apres']['actif'])->toBeFalse();
});

it('ne laisse pas la propriétaire changer son propre rôle', function () {
    livewire(EditUtilisateur::class, ['record' => $this->proprietaire->getRouteKey()])
        ->fillForm(['role' => Roles::LIVREUR, 'prenom' => 'Ndèye'])
        ->call('save');

    expect($this->proprietaire->fresh()->hasRole(Roles::PROPRIETAIRE))->toBeTrue()
        ->and($this->proprietaire->fresh()->prenom)->toBe('Ndèye');
});
