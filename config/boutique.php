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
    | Catalogue
    |--------------------------------------------------------------------------
    |
    | nouveaute_jours : durée du badge « Nouveau » après la première mise en
    | ligne. par_page : produits par page de la boutique (quatre par ligne
    | sur grand écran, cahier §5.2).
    |
    */

    'catalogue' => [
        'nouveaute_jours' => (int) env('BOUTIQUE_NOUVEAUTE_JOURS', 30),
        'par_page' => 24,
    ],

    /*
    |--------------------------------------------------------------------------
    | Recette (version d'essai en ligne, par exemple sur Vercel)
    |--------------------------------------------------------------------------
    |
    | Compte de la propriétaire créé par RecetteSeeder s'il n'existe pas.
    | Jamais en production : le premier compte s'y crée à la main
    | (php artisan amani:creer-personnel).
    |
    */

    'recette' => [
        'proprietaire' => [
            'email' => env('RECETTE_PROPRIETAIRE_EMAIL'),
            'mot_de_passe' => env('RECETTE_PROPRIETAIRE_MOT_DE_PASSE'),
            'prenom' => env('RECETTE_PROPRIETAIRE_PRENOM', 'Propriétaire'),
        ],
    ],

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

    // Menu de l'espace client (cahier §3, pages 15 à 21 ; maquette 8) : route et icône.
    'espace_client' => [
        'tableau' => ['mon-compte', 'maison'],
        'commandes' => ['mes-commandes', 'recu'],
        'favoris' => ['mes-favoris', 'coeur'],
        'points' => ['mes-points', 'boite'],
        'retours' => ['mes-retours', 'retour'],
        'avis' => ['mes-avis', 'etoile'],
        'profil' => ['mon-profil', 'compte'],
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
