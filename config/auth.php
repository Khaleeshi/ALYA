<?php

use App\Models\Cliente;
use App\Models\Utilisateur;

return [

    /*
    |--------------------------------------------------------------------------
    | Deux populations, deux portes
    |--------------------------------------------------------------------------
    |
    | « web » : les clientes de la boutique (table clientes), servies par Fortify.
    | « personnel » : la dirigeante, les administrateurs et les livreurs
    | (table utilisateurs), servis par l'administration Filament et, plus tard,
    | par l'espace livreur. Une session ouverte d'un côté n'ouvre jamais l'autre.
    |
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'clientes',
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
    | Lien valable 60 minutes, une demande par minute au plus.
    |
    */

    'passwords' => [
        'clientes' => [
            'provider' => 'clientes',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'utilisateurs' => [
            'provider' => 'utilisateurs',
            'table' => 'password_reset_tokens_personnel',
            'expire' => 30,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
