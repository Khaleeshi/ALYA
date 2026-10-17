<?php

use App\Models\Cliente;
use App\Models\Utilisateur;

return [

    /*
    |--------------------------------------------------------------------------
    | Deux populations, deux portes
    |--------------------------------------------------------------------------
    |
    | « web » : les clientes de la boutique (table clientes), connectées par
    | téléphone et code SMS (App\Http\Controllers\Comptes).
    | « personnel » : la propriétaire, les administrateurs et les livreurs
    | (table utilisateurs), servis par l'administration Filament et, plus tard,
    | par l'espace livreur. Une session ouverte d'un côté n'ouvre jamais l'autre.
    |
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'utilisateurs',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'clientes',
        ],

        'personnel' => [
            'driver' => 'session',
            'provider' => 'utilisateurs',
        ],
    ],

    'providers' => [
        'clientes' => [
            'driver' => 'eloquent',
            'model' => Cliente::class,
        ],

        'utilisateurs' => [
            'driver' => 'eloquent',
            'model' => Utilisateur::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Mots de passe oubliés
    |--------------------------------------------------------------------------
    |
    | Personnel seulement (les clientes n'ont pas de mot de passe) : lien
    | valable 30 minutes, une demande par minute au plus.
    |
    */

    'passwords' => [
        'utilisateurs' => [
            'provider' => 'utilisateurs',
            'table' => 'password_reset_tokens_personnel',
            'expire' => 30,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
