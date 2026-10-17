# AMANI Cosmetics

Boutique en ligne d'AMANI Cosmetics, première marque beauté d'ALYA Group, à Dakar : vitrine (Gloss, Skincare, Maquillage), espace cliente, administration, espace livreur, paiements Wave et Orange Money, livraison au Sénégal. Le projet technique est porté par Nogaye (compte GitHub Khaleeshi), qui lit le français : tout lui est expliqué en français, en phrases simples, sans jargon.

## Documents de référence

- `docs/Cahier des charges AMANI Cosmetics - 2026-10-17.docx` (et sa version texte `.md`) : **la référence**. « cahier §n » désigne sa section n. Les points marqués « à valider » ou listés en §21 ne sont pas des règles finales : ne pas les trancher sans la propriétaire.
- `docs/maquettes-amani/` : les cinq maquettes du thème choisi (accueil, fiche produit, collections, panier, livraison et paiement). Elles fixent le rendu ; les chiffres qu'elles affichent (notes, prix, nombre d'avis) sont des exemples.
- `docs/AUDIT ALYA COSMETIC - 2026-09-07.html` : audit de l'ancien cahier ALYA. Il reste utile pour les règles techniques que le nouveau cahier reprend (pas de commande ni de stock sans paiement confirmé, webhooks signés, réservation pendant le paiement, numérotation sans trou), pas pour la marque ni le design.
- `docs/archives-alya/` : ancien cahier et anciennes maquettes ALYA, pour mémoire.

Ordre de priorité en cas de contradiction : le cahier AMANI, puis les maquettes AMANI, puis l'audit.

## Décisions déjà prises

- Marque : AMANI Cosmetics (ALYA Group). Signature : « Parce que vous méritez tous de briller. » L'emblème (lotus ou couronne) attend le logo officiel ; en attendant, le composant `<x-amani.logo>` écrit la marque en lettres.
- Pile : Laravel 12, PHP 8.3, MySQL 8, Redis, vitrine Blade + Tailwind CSS 4 + Alpine.js, Livewire 3, administration Filament 4. Un seul dépôt, un seul déploiement.
- Tables et code métier nommés en français. Les colonnes imposées par le framework gardent leur nom (`password`, `remember_token`, `created_at`…).
- Fuseau Africa/Dakar, FCFA (XOF) seule monnaie de paiement, Sénégal uniquement.
- Français seul au lancement (cahier §1.2) ; l'anglais est prêt dans `lang/en` mais fermé (`BOUTIQUE_LANGUES=fr`).
- Comptes clientes sans mot de passe : téléphone sénégalais + code SMS (cahier §6.2). Le compte est obligatoire pour commander.
- Images : pilote Imagick (le GD d'Ubuntu ne sait pas écrire l'AVIF). Le serveur a besoin de `php8.3-imagick` et `libheif-plugin-aomenc`.

## Où se trouve quoi

- Deux gardes : `web` pour les clientes (`App\Models\Cliente`, `App\Http\Controllers\Comptes`), `personnel` pour la propriétaire, les administrateurs et les livreurs (`App\Models\Utilisateur`, Filament sur `/administration`, double authentification obligatoire).
- Codes SMS : `App\Support\Comptes\CodesConnexion` (6 chiffres, 10 minutes, usage unique, limites d'envoi). Envoi par `App\Support\Sms\EnvoiSms` ; le pilote `journal` écrit le SMS dans `storage/logs` et refuse la production. Pour un vrai prestataire : une classe qui implémente `EnvoiSms`, branchée dans `AppServiceProvider`.
- Verrouillage après cinq échecs (15 minutes) : `App\Support\Securite\VerrouillageConnexion`, pour la boutique et l'administration.
- Rôles et permissions : `App\Support\Acces\Roles` (propriétaire, administrateur, livreur) et `Permissions` ; la propriétaire a tout par `Gate::before`. Après un ajout de permission, relancer `RolesEtPermissionsSeeder`.
- Journal des actions sensibles en ajout seul : `JournalActivite::consigner(action, description, sujet, données)`. Le modèle et des déclencheurs en base refusent toute modification (MySQL : `log_bin_trust_function_creators = 1`).
- Langues : les routes de la vitrine se déclarent dans `Langues::routes()` (routes/web.php), morceaux d'adresse dans `lang/{fr,en}/chemins.php`, liens avec `route_langue('nom')`. Mêmes clés de texte en français et en anglais (un test le vérifie).
- Navigation : `config/boutique.php` (menu, aide, réseaux) ; un lien n'apparaît que si sa page existe.
- Photos : un modèle qui porte des photos implémente `HasMedia` et utilise le trait `App\Models\Concerns\AvecPhotos` ; l'affichage passe par `<x-amani.image>`.
- Système de design : jetons dans `resources/css/amani/jetons.css`, composants dans `resources/views/components/amani/`, vitrine des composants sur `/systeme-de-design` (hors production).

## Règles de travail

- Design des maquettes AMANI : crème et nude, texte brun #493729, accents or #C6A15B, coins légèrement arrondis, boutons en pilule (dégradé bronze vers or), Playfair Display pour les titres, Jost pour le texte, Great Vibes pour la signature manuscrite. L'or est trop clair pour du texte : textes dorés et boutons en bronze. Contraste du texte ≥ 4,5.
- Aucune donnée de démonstration en production (cahier §13 et §17) : coordonnées, réseaux, logos de paiement, avis et chiffres viennent d'AMANI ou ne s'affichent pas.
- Règle fondamentale (cahier §6.3) : la commande définitive, son numéro et la sortie de stock n'existent qu'après la confirmation réelle du paiement par le prestataire.
- Jamais de secret dans le code ; `.env` est ignoré ; `.env.example` documente chaque variable.
- Tests : `vendor/bin/pest` (SQLite en mémoire) ; l'intégration continue les rejoue sur MySQL 8. `vendor/bin/pint` avant chaque enregistrement.
- Messages de commit en français, une phrase qui dit ce que la personne obtient.
