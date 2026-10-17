<?php

use Laravel\Fortify\Features;

/*
|--------------------------------------------------------------------------
| Fortify : comptes des clientes
|--------------------------------------------------------------------------
|
| Fortify ne sert que la boutique (garde « web », table clientes).
| Ses routes ne sont pas enregistrées automatiquement : elles sont déclarées
| dans routes/web.php, sous /fr et /en, avec des adresses traduites.
| L'administration a sa propre connexion (Filament, double authentification).
|
*/

return [

    'guard' => 'web',

    'passwords' => 'clientes',

    // Le champ de connexion accepte un email ou un numéro de téléphone (décision D10).
    'username' => 'identifiant',

    'email' => 'email',

    'lowercase_usernames' => true,

    // Remplacé à l'exécution par l'adresse localisée de l'espace cliente.
    'home' => '/fr/mon-compte',

    'prefix' => '',

    'domain' => null,

    'middleware' => ['web'],

    // Pas de limiteur de route : le verrouillage après cinq échecs est assuré
    // par App\Support\Securite\VerrouillageConnexion dans le parcours de connexion.
    'limiters' => [
        'login' => null,
    ],

    'views' => true,

    'features' => [
        Features::registration(),
        Features::resetPasswords(),
    ],

];
