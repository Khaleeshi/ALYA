<?php

/*
 * Réglages de la version d'essai sur Vercel (recette). Rien de secret ici.
 * Chargés par api/index.php (site) et construire.php (mise en ligne), sans
 * écraser une variable déjà réglée dans Vercel.
 *
 * Les secrets se règlent dans Vercel (Settings → Environment Variables) :
 *   APP_KEY                              clé de chiffrement du site
 *   DATABASE_URL_UNPOOLED                ajoutée seule par la base Neon (Storage)
 *   RECETTE_PROPRIETAIRE_EMAIL           compte de la propriétaire de la recette
 *   RECETTE_PROPRIETAIRE_MOT_DE_PASSE    son mot de passe (12 caractères au moins)
 */

$reglages = [
    'APP_NAME' => 'AMANI Cosmetics',
    'APP_ENV' => 'recette',
    'APP_DEBUG' => 'false',
    'APP_LOCALE' => 'fr',
    'APP_FALLBACK_LOCALE' => 'fr',
    'BOUTIQUE_LANGUES' => 'fr',

    // Le disque de Vercel est en lecture seule, sauf /tmp.
    'APP_CONFIG_CACHE' => '/tmp/config.php',
    'APP_EVENTS_CACHE' => '/tmp/events.php',
    'APP_PACKAGES_CACHE' => '/tmp/packages.php',
    'APP_ROUTES_CACHE' => '/tmp/routes.php',
    'APP_SERVICES_CACHE' => '/tmp/services.php',
    'VIEW_COMPILED_PATH' => '/tmp/vues',

    'LOG_CHANNEL' => 'stderr',
    'LOG_LEVEL' => 'info',

    // Base PostgreSQL Neon (Vercel → Storage) ; son adresse arrive par DATABASE_URL_UNPOOLED.
    'DB_CONNECTION' => 'pgsql',
    'DB_SSLMODE' => 'require',

    'SESSION_DRIVER' => 'database',
    'SESSION_SECURE_COOKIE' => 'true',
    'CACHE_STORE' => 'database',
    'QUEUE_CONNECTION' => 'sync',

    // Aucun SMS ne part : le code de connexion s'affiche à l'écran (recette seulement).
    'SMS_PILOTE' => 'journal',
    'SMS_AFFICHER_EN_RECETTE' => 'true',
];

foreach ($reglages as $cle => $valeur) {
    if (getenv($cle) === false) {
        putenv($cle.'='.$valeur);
        $_ENV[$cle] = $_SERVER[$cle] = $valeur;
    }
}
