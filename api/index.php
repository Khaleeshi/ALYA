<?php

/*
 * Porte d'entrée du site sur Vercel (module vercel-php, voir vercel.json).
 *
 * Les réglages de la recette (deploiement/vercel/reglages.php) sont chargés
 * d'abord. Vercel envoie ici toutes les adresses. Les fichiers de public/ (styles et
 * scripts compilés, polices, images, favicon) sont renvoyés tels quels ;
 * tout le reste passe par Laravel, comme avec public/index.php sur un
 * serveur classique.
 */

require __DIR__.'/../deploiement/vercel/reglages.php';

$public = realpath(__DIR__.'/../public');
$chemin = rawurldecode((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
$fichier = $chemin === '/' ? false : realpath($public.$chemin);

if ($fichier !== false
    && str_starts_with($fichier, $public.DIRECTORY_SEPARATOR)
    && is_file($fichier)
    && strtolower(pathinfo($fichier, PATHINFO_EXTENSION)) !== 'php') {
    $types = [
        'css' => 'text/css; charset=utf-8',
        'js' => 'text/javascript; charset=utf-8',
        'mjs' => 'text/javascript; charset=utf-8',
        'json' => 'application/json',
        'map' => 'application/json',
        'woff2' => 'font/woff2',
        'woff' => 'font/woff',
        'ttf' => 'font/ttf',
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'webp' => 'image/webp',
        'avif' => 'image/avif',
        'gif' => 'image/gif',
        'ico' => 'image/x-icon',
        'txt' => 'text/plain; charset=utf-8',
    ];

    header('Content-Type: '.($types[strtolower(pathinfo($fichier, PATHINFO_EXTENSION))] ?? 'application/octet-stream'));
    header('Content-Length: '.filesize($fichier));
    // Les fichiers compilés par Vite portent leur empreinte dans leur nom : ils ne changent jamais.
    header('Cache-Control: '.(str_starts_with($chemin, '/build/') ? 'public, max-age=31536000, immutable' : 'public, max-age=3600'));
    readfile($fichier);

    return;
}

require $public.'/index.php';
