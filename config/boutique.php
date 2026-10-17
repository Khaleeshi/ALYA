<?php

return [

    'nom' => 'AMANI Cosmetics',

    /*
    |--------------------------------------------------------------------------
    | Langues ouvertes
    |--------------------------------------------------------------------------
    |
    | Français au lancement (cahier §1.2). La version anglaise est prête dans
    | lang/en mais reste fermée tant qu'elle n'est pas confirmée : pour l'ouvrir,
    | BOUTIQUE_LANGUES=fr,en dans le fichier .env.
    |
    */

    'langues' => array_values(array_filter(array_map('trim', explode(',', (string) env('BOUTIQUE_LANGUES', 'fr'))))),

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    |
    | Menu principal (cahier §4.1), liens d'aide et liens du bandeau de service.
    | Clé de texte dans lang/fr/vitrine.php => nom de route. Une entrée dont la
    | page n'existe pas encore n'est pas affichée : aucun lien mort.
    |
    */

    'navigation' => [
        'accueil' => 'accueil',
        'boutique' => 'boutique',
        'collections' => 'collections',
        'editions-limitees' => 'editions-limitees',
        'coffrets' => 'coffrets',
        'guide' => 'guide',
        'a-propos' => 'a-propos',
    ],

    'aide' => [
        'faq' => 'faq',
        'contact' => 'contact',
        'suivi' => 'suivi-commande',
    ],

    /*
    |--------------------------------------------------------------------------
    | Réseaux et contact
    |--------------------------------------------------------------------------
    |
    | Liens officiels à fournir par AMANI (cahier §13) : un lien vide n'est pas
    | affiché, aucune coordonnée de démonstration en production.
    |
    */

    'reseaux' => [
        'Instagram' => env('AMANI_INSTAGRAM'),
        'TikTok' => env('AMANI_TIKTOK'),
        'WhatsApp' => env('AMANI_WHATSAPP'),
    ],

];
