<?php

use App\Http\Middleware\DefinirLangue;
use App\Support\Langues;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'langue' => DefinirLangue::class,
        ]);

        // Une cliente non connectée va vers la connexion, une cliente déjà
        // connectée vers son compte, dans la langue de la page demandée.
        // L'administration gère ses propres redirections (Filament).
        $middleware->redirectGuestsTo(fn (Request $request): string => route_langue('connexion', langue: Langues::depuisRequete($request)));
        $middleware->redirectUsersTo(fn (Request $request): string => route_langue('mon-compte', langue: Langues::depuisRequete($request)));

        // Le cookie de langue n'a rien de secret : il reste lisible.
        $middleware->encryptCookies(except: [DefinirLangue::COOKIE]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
