<?php

use App\Http\Controllers\Boutique\AccueilController;
use App\Http\Controllers\Boutique\BoutiqueController;
use App\Http\Controllers\Boutique\CollectionsController;
use App\Http\Controllers\Boutique\ProduitController;
use App\Http\Controllers\Comptes\ConnexionController;
use App\Http\Controllers\Comptes\InscriptionController;
use App\Http\Controllers\Comptes\ProfilController;
use App\Http\Middleware\DefinirLangue;
use App\Support\Langues;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// L'adresse nue envoie vers la langue retenue (compte, puis cookie), le français sinon.
Route::get('/', function (Request $request) {
    $langue = $request->user('web')?->langue ?? $request->cookie(DefinirLangue::COOKIE);

    return redirect(route_langue('accueil', langue: Langues::estDisponible($langue) ? $langue : Langues::PAR_DEFAUT));
})->name('racine');

Langues::routes(function (string $langue): void {
    $chemin = fn (string $cle): string => Langues::chemin($cle, $langue);
    $code = $chemin('connexion').'/'.$chemin('code');

    Route::get('/', AccueilController::class)->name('accueil');

    // Catalogue (cahier §3 et §5) : les adresses de produits et de collections portent leur slug.
    Route::get($chemin('boutique'), [BoutiqueController::class, 'index'])->name('boutique');
    Route::get($chemin('boutique').'/{categorie}', [BoutiqueController::class, 'categorie'])->name('boutique.categorie');
    Route::get($chemin('produit').'/{produit}', [ProduitController::class, 'show'])->name('boutique.produit');
    Route::get($chemin('collections'), [CollectionsController::class, 'index'])->name('collections');
    Route::get($chemin('collections').'/{gamme}', [CollectionsController::class, 'show'])->name('collections.gamme');
    Route::get($chemin('editions-limitees'), [BoutiqueController::class, 'editionsLimitees'])->name('editions-limitees');
    Route::get($chemin('coffrets'), [BoutiqueController::class, 'coffrets'])->name('coffrets');

    // Compte cliente : téléphone, code SMS, puis création du compte si besoin.
    Route::middleware('guest:web')->group(function () use ($chemin, $code): void {
        Route::get($chemin('connexion'), [ConnexionController::class, 'formulaire'])->name('connexion');
        Route::post($chemin('connexion'), [ConnexionController::class, 'envoyerCode'])
            ->middleware('throttle:comptes')
            ->name('connexion.envoyer');

        Route::get($code, [ConnexionController::class, 'formulaireCode'])->name('connexion.code');
        Route::post($code, [ConnexionController::class, 'verifierCode'])
            ->middleware('throttle:comptes')
            ->name('connexion.verifier');
        Route::post($code.'/'.$chemin('renvoyer'), [ConnexionController::class, 'renvoyerCode'])
            ->middleware('throttle:comptes')
            ->name('connexion.renvoyer');

        Route::get($chemin('inscription'), [InscriptionController::class, 'formulaire'])->name('inscription');
        Route::post($chemin('inscription'), [InscriptionController::class, 'enregistrer'])
            ->middleware('throttle:comptes')
            ->name('inscription.enregistrer');
    });

    Route::middleware('auth:web')->group(function () use ($chemin): void {
        Route::view($chemin('mon-compte'), 'comptes.mon-compte')->name('mon-compte');
        Route::get($chemin('mon-profil'), [ProfilController::class, 'formulaire'])->name('mon-profil');
        Route::put($chemin('mon-profil'), [ProfilController::class, 'enregistrer'])->name('mon-profil.enregistrer');
        Route::post($chemin('deconnexion'), [ConnexionController::class, 'deconnexion'])->name('deconnexion');
    });
});

// Bibliothèque de composants AMANI, pour la validation par la direction.
// Ouverte en développement et en recette, jamais en production.
if (! app()->isProduction()) {
    Route::view('/systeme-de-design', 'design.systeme')
        ->middleware('langue:fr')
        ->name('systeme-de-design');
}
