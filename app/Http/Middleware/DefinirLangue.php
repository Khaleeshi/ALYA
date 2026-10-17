<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fixe la langue d'après le préfixe de l'adresse (/fr, /en) et la retient :
 * dans un cookie pour toutes, et sur le compte d'une cliente connectée.
 */
class DefinirLangue
{
    public const COOKIE = 'langue';

    public function handle(Request $request, Closure $next, string $langue): Response
    {
        App::setLocale($langue);
        Carbon::setLocale($langue);

        if ($request->cookie(self::COOKIE) !== $langue) {
            Cookie::queue(self::COOKIE, $langue, 60 * 24 * 365);
        }

        $cliente = $request->user('web');

        if ($cliente !== null && $cliente->langue !== $langue) {
            $cliente->forceFill(['langue' => $langue])->saveQuietly();
        }

        return $next($request);
    }
}
