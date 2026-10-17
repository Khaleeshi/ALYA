<?php

use App\Exceptions\JournalImmuable;
use App\Models\Cliente;
use App\Models\JournalActivite;
use App\Models\Utilisateur;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

it('signe « Système » une action sans personne connectée', function () {
    $ligne = JournalActivite::consigner('stock.alerte', 'Stock critique');

    expect($ligne->auteur_type)->toBe(JournalActivite::AUTEUR_SYSTEME)
        ->and($ligne->auteur_libelle)->toBe('Système')
        ->and($ligne->cree_le)->not->toBeNull();
});

it('signe « ALYA » pour la dirigeante et « Administrateur · prénom » sinon', function () {
    $this->actingAs(dirigeante(), 'personnel');
    expect(JournalActivite::consigner('test', 'Action de la dirigeante')->auteur_libelle)->toBe('ALYA');

    $this->actingAs(administrateur(['prenom' => 'Moussa']), 'personnel');
    expect(JournalActivite::consigner('test', "Action de l'administrateur")->auteur_libelle)->toBe('Administrateur · Moussa');

    $livreur = Utilisateur::factory()->livreur()->create(['prenom' => 'Ibou']);
    expect(JournalActivite::consigner('test', 'Livraison', auteur: $livreur)->auteur_libelle)->toBe('Livreur · Ibou');
});

it('garde le lien vers ce qui est concerné', function () {
    $cliente = Cliente::factory()->create();

    $ligne = JournalActivite::consigner('test', 'Concerne une cliente', $cliente);

    expect($ligne->sujet_type)->toBe('cliente')
        ->and($ligne->sujet->is($cliente))->toBeTrue();
});

it('refuse toute modification par le modèle', function () {
    $ligne = JournalActivite::consigner('test', 'Ligne originale');

    $ligne->update(['description' => 'Ligne réécrite']);
})->throws(JournalImmuable::class);

it('refuse toute suppression par le modèle', function () {
    JournalActivite::consigner('test', 'Ligne à garder')->delete();
})->throws(JournalImmuable::class);

it('refuse une modification directe en base de données', function () {
    JournalActivite::consigner('test', 'Ligne originale');

    DB::table('journal_activites')->update(['description' => 'Ligne réécrite']);
})->throws(QueryException::class);

it('refuse une suppression directe en base de données', function () {
    JournalActivite::consigner('test', 'Ligne à garder');

    DB::table('journal_activites')->delete();
})->throws(QueryException::class);
