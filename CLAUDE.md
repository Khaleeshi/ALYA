# ALYA Cosmetic

Boutique en ligne premium (noir profond + or champagne) pour ALYA Cosmetic, à Dakar : vitrine, espace cliente, administration, espace livreur, paiements Wave et Orange Money, notifications WhatsApp. Le projet technique est porté par Nogaye (compte GitHub Khaleeshi), qui lit le français : tout lui est expliqué en français, en phrases simples, sans jargon.

## Documents de référence

- `docs/CAHIER DES CHARGES COMPLET — ALYA C.txt` : les 63 sections écrites par la cliente ALYA. « CDC §n » désigne la section n.
- `docs/AUDIT ALYA COSMETIC - 2026-09-07.html` : lecture critique du cahier des charges et des maquettes. Il tranche les règles métier (section 6), l'architecture (section 7), le plan en huit phases (section 8), la direction artistique et le système de design (section 9), les exigences mesurables (10) et la recette (11). Les seize décisions D1 à D16 de la section 5 appartiennent à la direction ALYA : tant qu'elle n'a pas tranché autrement, appliquer la recommandation de l'audit.
- `docs/maquettes/PHOTO-*.jpg` : les dix maquettes auditées (la section 9.4 de l'audit les désigne par leur nom, « Fichier 22 (3) » = `PHOTO-2026-08-09-13-31-22 (3).jpg`). Elles servent à lister les contenus voulus, jamais à être copiées : leur rendu est banni par la section 9.2.

Ordre de priorité en cas de contradiction : l'audit, puis le cahier des charges, puis les maquettes.

## Décisions déjà prises

- La marque s'écrit ALYA (le dossier s'appelle AYLA par erreur de frappe).
- Pile : Laravel 12, PHP 8.3, MySQL 8, Redis, vitrine Blade + Tailwind CSS 4 + Alpine.js, Livewire 3, back-office Filament 4, espace livreur en PWA (audit section 7). Un seul dépôt, un seul déploiement.
- Tables et code métier nommés en français, comme la section 7.4 de l'audit. Les colonnes imposées par le framework gardent leur nom (`password`, `remember_token`, `created_at`…).
- Fuseau Africa/Dakar, FCFA (XOF) comme seule monnaie de paiement, français par défaut et anglais complet.
- Images : pilote Imagick (le GD d'Ubuntu ne sait pas écrire l'AVIF). Le serveur a besoin des paquets `php8.3-imagick` et `libheif-plugin-aomenc`.

## Où se trouve quoi (phase 1)

- Deux gardes : `web` pour les clientes (`App\Models\Cliente`, Fortify), `personnel` pour la dirigeante, les administrateurs et les livreurs (`App\Models\Utilisateur`, Filament sur `/administration`, double authentification obligatoire).
- Rôles et permissions : `App\Support\Acces\Roles` et `Permissions` ; la dirigeante a tout par `Gate::before` (AppServiceProvider). Après un ajout de permission, relancer `RolesEtPermissionsSeeder`.
- Verrouillage après cinq échecs (15 minutes) : `App\Support\Securite\VerrouillageConnexion`, utilisé par Fortify et par `App\Filament\Pages\Auth\Connexion`.
- Journal d'activité en ajout seul : `JournalActivite::consigner(action, description, sujet, données)`. Le modèle et des déclencheurs en base refusent toute modification ou suppression (MySQL : `log_bin_trust_function_creators = 1`).
- Langues : toute route de la vitrine se déclare dans `Langues::routes()` (routes/web.php), avec ses morceaux d'adresse traduits dans `lang/{fr,en}/chemins.php`. Dans les vues, `route_langue('nom')`. Les textes d'interface vont dans `lang/{fr,en}/*.php` avec les mêmes clés des deux côtés (un test le vérifie).
- Photos : un modèle qui porte des photos implémente `HasMedia` et utilise le trait `App\Models\Concerns\AvecPhotos` ; l'affichage passe par `<x-alya.image>`.
- Système de design : jetons dans `resources/css/alya/jetons.css`, composants Blade dans `resources/views/components/alya/`, vitrine des composants sur `/systeme-de-design` (hors production).

## Règles de travail

- Système de design de la section 9.3 : palette fixée (noir profond #070707, or champagne #C8A95A, ivoire #F3EDE2…), Bodoni Moda pour les titres et les prix, Jost pour le texte, angles vifs, aucune ombre portée, un seul bouton doré plein par écran, aucune particule en CSS, aucun émoji dans l'interface, aucun chiffre inventé.
- La règle fondamentale du CDC §62 ne se discute pas : pas de numéro de commande ni de sortie de stock avant la confirmation réelle du paiement.
- Jamais de secret dans le code ; `.env` est ignoré ; `.env.example` documente chaque variable.
- Tests : `vendor/bin/pest` (SQLite en mémoire) ; l'intégration continue les rejoue sur MySQL 8. `vendor/bin/pint` avant chaque enregistrement.
- Messages de commit en français, une phrase qui dit ce que la personne obtient.
