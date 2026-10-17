<?php

use App\Http\Middleware\DefinirLangue;
use App\Support\Langues;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;

// L'adresse nue envoie vers la langue retenue (compte, puis cookie), le français sinon.
Route::get('/', function (Request $request) {
    $langue = $request->user('web')?->langue ?? $request->cookie(DefinirLangue::COOKIE);

    return redirect(route_langue('accueil', langue: Langues::estDisponible($langue) ? $langue : Langues::PAR_DEFAUT));
})->name('racine');

Langues::routes(function (string $langue): void {
    $chemin = fn (string $cle): string => Langues::chemin($cle, $langue);

    Route::view('/', 'vitrine.accueil')->name('accueil');

    // Comptes clientes (Fortify). Les écrans sont dans resources/views/comptes.
    Route::middleware('guest:web')->group(function () use ($chemin): void {
        Route::get($chemin('connexion'), [AuthenticatedSessionController::class, 'create'])->name('connexion');
        Route::post($chemin('connexion'), [AuthenticatedSessionController::class, 'store'])
            ->middleware('throttle:comptes')
            ->name('connexion.envoyer');

        Route::get($chemin('inscription'), [RegisteredUserController::class, 'create'])->name('inscription');
        Route::post($chemin('inscription'), [RegisteredUserController::class, 'store'])
            ->middleware('throttle:comptes')
            ->name('inscription.envoyer');

        Route::get($chemin('mot-de-passe-oublie'), [PasswordResetLinkController::class, 'create'])->name('mot-de-passe-oublie');
        Route::post($chemin('mot-de-passe-oublie'), [PasswordResetLinkController::class, 'store'])
            ->middleware('throttle:comptes')
            ->name('mot-de-passe-oublie.envoyer');

        Route::get($chemin('reinitialiser-mot-de-passe').'/{token}', [NewPasswordController::class, 'create'])->name('reinitialiser-mot-de-passe');
        Route::post($chemin('reinitialiser-mot-de-passe'), [NewPasswordController::class, 'store'])
            ->middleware('throttle:comptes')
            ->name('reinitialiser-mot-de-passe.envoyer');
    });

    Route::middleware('auth:web')->group(function () use ($chemin): void {
        Route::view($chemin('mon-compte'), 'comptes.mon-compte')->name('mon-compte');
        Route::post($chemin('deconnexion'), [AuthenticatedSessionController::class, 'destroy'])->name('deconnexion');
    });
});

// Bibliothèque de composants ALYA, pour la validation par la direction.
// Ouverte en développement et en recette, jamais en production.
if (! app()->isProduction()) {
    Route::view('/systeme-de-design', 'design.systeme')
        ->middleware('langue:fr')
        ->name('systeme-de-design');
}
