<?php

/*
 * Mise en ligne sur Vercel (lancé par « composer run vercel », après
 * « composer install --no-dev » du module vercel-php) :
 * 1. fichiers de Filament (styles et scripts de l'administration) ;
 * 2. styles et scripts du site (Vite) ;
 * 3. base de données à jour et remplie (php artisan amani:preparer-recette).
 * La première étape qui échoue arrête la mise en ligne, avec son message.
 */

require __DIR__.'/reglages.php';

chdir(dirname(__DIR__, 2));

$php = escapeshellarg(PHP_BINARY);
$etapes = [
    'Fichiers de l’administration' => "{$php} artisan filament:assets --ansi",
    'Paquets JavaScript' => 'npm ci --no-audit --no-fund',
    'Styles et scripts du site' => 'npm run build',
    'Base de données' => "{$php} artisan amani:preparer-recette --ansi",
];

foreach ($etapes as $nom => $commande) {
    echo PHP_EOL."▶ {$nom}".PHP_EOL;
    passthru($commande, $code);

    if ($code !== 0) {
        fwrite(STDERR, PHP_EOL."✗ Échec : {$nom}".PHP_EOL);

        exit($code);
    }
}

echo PHP_EOL.'✓ Version d’essai construite.'.PHP_EOL;
