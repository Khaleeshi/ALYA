<?php

use App\Models\Utilisateur;
use App\Support\Acces\Permissions;
use App\Support\Acces\Roles;
use Spatie\Permission\Models\Role;

it('donne toutes les permissions à la dirigeante', function () {
    $dirigeante = dirigeante();

    foreach (Permissions::toutes() as $permission) {
        expect($dirigeante->can($permission))->toBeTrue("La dirigeante devrait avoir {$permission}");
    }
});

it("réserve l'espace achats, les remboursements et la gestion du personnel à la dirigeante", function () {
    $administrateur = administrateur();

    expect($administrateur->can(Permissions::ACHATS_GERER))->toBeFalse()
        ->and($administrateur->can(Permissions::REMBOURSEMENTS_EXECUTER))->toBeFalse()
        ->and($administrateur->can(Permissions::UTILISATEURS_GERER))->toBeFalse()
        ->and($administrateur->can(Permissions::COMMANDES_GERER))->toBeTrue()
        ->and($administrateur->can(Permissions::JOURNAL_VOIR))->toBeTrue();
});

it("ouvre l'espace achats à un administrateur quand la dirigeante le lui accorde", function () {
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

it('retire tout à une dirigeante dont le compte est désactivé', function () {
    $dirigeante = Utilisateur::factory()->dirigeante()->inactif()->create();

    expect($dirigeante->can(Permissions::ACHATS_GERER))->toBeFalse()
        ->and($dirigeante->canAccessPanel(filament()->getPanel('administration')))->toBeFalse();
});

it("montre la gestion du personnel à la dirigeante et la refuse à l'administrateur", function () {
    $secret = secretDoubleAuthentification();

    $this->actingAs(dirigeante(['app_authentication_secret' => $secret]), 'personnel')
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
        ->toBe(['administrateur', 'dirigeante', 'livreur']);
});
