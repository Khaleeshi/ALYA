<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // Codes de connexion des clientes (cahier §6.2). « journal » écrit le SMS
    // dans storage/logs au lieu de l'envoyer : développement et recette
    // seulement, refusé en production. Le prestataire réel reste à choisir.
    'sms' => [
        'pilote' => env('SMS_PILOTE', 'journal'),
        'expediteur' => env('SMS_EXPEDITEUR', 'AMANI'),
        // Recette seulement (jamais en production) : le SMS du pilote « journal »
        // s'affiche aussi à l'écran, pour essayer la connexion sans téléphone.
        'afficher_en_recette' => (bool) env('SMS_AFFICHER_EN_RECETTE', false),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
