<?php

use App\Models\Utilisateur;
use App\Support\Acces\Permissions;
use App\Support\Acces\Roles;
use Spatie\Permission\Models\Role;

it('donne toutes les permissions à la propriétaire', function () {
    $proprietaire = proprietaire();

    foreach (Permissions::toutes() as $permission) {
        expect($proprietaire->can($permission))->toBeTrue("La propriétaire devrait avoir {$permission}");
    }
});

it("réserve l'espace achats, les remboursements et la gestion du personnel à la propriétaire", function () {
    $administrateur = administrateur();

    expect($administrateur->can(Permissions::ACHATS_GERER))->toBeFalse()
        ->and($administrateur->can(Permissions::REMBOURSEMENTS_EXECUTER))->toBeFalse()
        ->and($administrateur->can(Permissions::UTILISATEURS_GERER))->toBeFalse()
        ->and($administrateur->can(Permissions::COMMANDES_GERER))->toBeTrue()
        ->and($administrateur->can(Permissions::JOURNAL_VOIR))->toBeTrue();
});

it("ouvre l'espace achats à un administrateur quand la propriétaire le lui accorde", function () {
    $administrateur = administrateur();
    $administrateur->givePermissionTo(Permissions::ACHATS_GERER);

    expect($administrateur->fresh()->can(Permissions::ACHATS_GERER))->toBeTrue();
});

it("ne donne au livreur que l'espace livreur", function () {
    $livreur = Utilisateur::factory()->livreur()->create();

    expect($livreur->can(Permissions::LIVRAISONS_EFFECTUER))->toBeTrue()
        ->and($livreur->can(Permissions::COMMANDES_GERER))->toBeFalse()
        ->and($livreur->canAccessPanel(filament()->getPanel('administration')))->toBeFalse();
});

it('retire tout à une propriétaire dont le compte est désactivé', function () {
    $proprietaire = Utilisateur::factory()->proprietaire()->inactif()->create();

    expect($proprietaire->can(Permissions::ACHATS_GERER))->toBeFalse()
        ->and($proprietaire->canAccessPanel(filament()->getPanel('administration')))->toBeFalse();
});

it("montre la gestion du personnel à la propriétaire et la refuse à l'administrateur", function () {
    $secret = secretDoubleAuthentification();

    $this->actingAs(proprietaire(['app_authentication_secret' => $secret]), 'personnel')
        ->get('/administration/personnel')
        ->assertOk();

    $this->actingAs(administrateur(['app_authentication_secret' => $secret]), 'personnel')
        ->get('/administration/personnel')
        ->assertForbidden();
});

it("montre le journal à l'administrateur, sans aucun bouton pour le modifier", function () {
    $this->actingAs(administrateur(['app_authentication_secret' => secretDoubleAuthentification()]), 'personnel')
        ->get('/administration/journal')
        ->assertOk();

    expect(Route::has('filament.administration.resources.journal.create'))->toBeFalse()
        ->and(Route::has('filament.administration.resources.journal.edit'))->toBeFalse();
});

it('connaît exactement les trois rôles du personnel', function () {
    expect(Role::where('guard_name', Roles::GARDE)->pluck('name')->sort()->values()->all())
        ->toBe(['administrateur', 'livreur', 'proprietaire']);
});
