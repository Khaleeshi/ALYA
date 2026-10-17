<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

/**
 * Liens de navigation de la vitrine (config/boutique.php), limités aux pages
 * qui existent déjà dans la langue affichée.
 */
final class Navigation
{
    /**
     * @return list<array{cle: string, libelle: string, url: string, actif: bool}>
     */
    public static function principale(): array
    {
        return self::liens('navigation', 'vitrine.navigation.');
    }

    /**
     * @return list<array{cle: string, libelle: string, url: string, actif: bool}>
     */
    public static function aide(): array
    {
        return self::liens('aide', 'vitrine.bandeau.');
    }

    /**
     * Menu de l'espace client, limité aux pages déjà construites.
     *
     * @return list<array{cle: string, libelle: string, url: string, icone: string, actif: bool}>
     */
    public static function espaceClient(): array
    {
        $langue = app()->getLocale();
        $courante = request()->route()?->getName();
        $liens = [];

        foreach ((array) config('boutique.espace_client') as $cle => [$route, $icone]) {
            if (! Route::has($langue.'.'.$route)) {
                continue;
            }

            $liens[] = [
                'cle' => $cle,
                'libelle' => __('comptes.espace.menu.'.$cle),
                'url' => route_langue($route),
                'icone' => $icone,
                'actif' => $courante === $langue.'.'.$route,
            ];
        }

        return $liens;
    }

    /**
     * @return array<string, string> nom du réseau => adresse
     */
    public static function reseaux(): array
    {
        return array_filter((array) config('boutique.reseaux'), fn (?string $url): bool => filled($url));
    }

    /**
     * @return list<array{cle: string, libelle: string, url: string, actif: bool}>
     */
    private static function liens(string $groupe, string $prefixeTexte): array
    {
        $langue = app()->getLocale();
        $courante = request()->route()?->getName();
        $liens = [];

        foreach ((array) config('boutique.'.$groupe) as $cle => $route) {
            if (! Route::has($langue.'.'.$route)) {
                continue;
            }

            $liens[] = [
                'cle' => $cle,
                'libelle' => __($prefixeTexte.$cle),
                'url' => route_langue($route),
                // « Boutique » reste en surbrillance sur ses pages filles (catégorie, fiche produit).
                'actif' => $courante === $langue.'.'.$route || str_starts_with((string) $courante, $langue.'.'.$route.'.'),
            ];
        }

        return $liens;
    }
}
