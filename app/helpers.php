<?php

use App\Support\Langues;

if (! function_exists('route_langue')) {
    /**
     * Adresse d'une route de la vitrine dans la langue courante :
     * route_langue('connexion') donne /fr/connexion ou /en/login.
     */
    function route_langue(string $nom, array $parametres = [], ?string $langue = null, bool $absolue = true): string
    {
        return Langues::route($nom, $parametres, $langue, $absolue);
    }
}
