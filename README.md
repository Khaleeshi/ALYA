# ALYA Cosmetic

Boutique en ligne d'ALYA Cosmetic, à Dakar : vitrine en français et en anglais, espace cliente, administration, espace livreur, paiements Wave et Orange Money, notifications WhatsApp.

Le cahier des charges, l'audit du 7 septembre 2026 et les maquettes sont dans [`docs/`](docs/). L'audit fixe le plan en huit phases ; ce dépôt en est à la **phase 1 : fondations et système de design**.

## Ce que contient la phase 1

| Brique | Où |
|---|---|
| Laravel 12, MySQL 8, Redis, files de travaux (Horizon sur `/administration/files`) | `config/`, `bootstrap/` |
| Comptes clientes : inscription, connexion par email ou téléphone, mot de passe oublié, verrouillage 15 minutes après 5 échecs | `routes/web.php`, `app/Providers/FortifyServiceProvider.php`, `resources/views/comptes/` |
| Administration (`/administration`) : double authentification obligatoire, rôles dirigeante / administrateur / livreur, permissions fines, gestion du personnel | `app/Filament/`, `app/Support/Acces/` |
| Journal d'activité en ajout seul (impossible à modifier ou supprimer, même en base) | `app/Models/JournalActivite.php` |
| Chaîne d'images : chaque photo déclinée en 5 largeurs (400 à 2400 px), en AVIF et en WebP | `app/Support/Medias/DeclinaisonsImages.php` |
| Français et anglais : `/fr/connexion`, `/en/login`, langue retenue | `app/Support/Langues.php`, `lang/` |
| Système de design ALYA : couleurs, polices auto-hébergées, composants Blade | `resources/css/alya/`, `resources/views/components/alya/`, page `/systeme-de-design` |
| 87 tests automatiques, rejoués sur GitHub à chaque envoi | `tests/`, `.github/workflows/tests.yml` |

## Installer le projet sur un poste de développement

Prérequis : PHP 8.3 avec les extensions `imagick`, `intl`, `pdo_mysql`, `redis`, `zip` ; Composer ; Node 22 ; MySQL 8 ; Redis.
Sur Ubuntu 24.04, l'écriture des images AVIF demande en plus le paquet `libheif-plugin-aomenc`.

```bash
composer setup          # dépendances, .env, clé, base, rôles, lien de stockage, compilation
php artisan alya:creer-personnel   # crée le compte de la dirigeante (mot de passe saisi à la main)
composer dev            # serveur, files de travaux, journaux et Vite en même temps
```

Puis ouvrir http://localhost:8000 (boutique), http://localhost:8000/administration (administration) et http://localhost:8000/systeme-de-design (bibliothèque de composants).

### Réglage MySQL indispensable

Le journal d'activité est protégé par des déclencheurs en base. Si MySQL refuse de les créer (« You do not have the SUPER privilege and binary logging is enabled »), lancer une fois, en administrateur MySQL :

```sql
SET PERSIST log_bin_trust_function_creators = 1;
```

## Tests

```bash
vendor/bin/pest          # tous les tests (base SQLite en mémoire)
vendor/bin/pint          # mise en forme du code
```

Sur GitHub, chaque envoi relance les tests sur MySQL 8 et vérifie la mise en forme.

## Environnements

| Nom (`APP_ENV`) | Rôle |
|---|---|
| `local` | Développement. |
| `recette` | Validation par la direction avant chaque mise en production. La page `/systeme-de-design` y est ouverte. |
| `production` | Le site public. Aucun test n'y est fait. |

Le déploiement automatique (serveur, nginx, Cloudflare, sauvegardes) sera branché dès que le serveur de recette sera choisi (démarches de la phase 0).

## Ce qui reste à fournir par ALYA pour la suite

Voir la section 12 de l'audit : décisions D1 à D16, logo vectoriel, photos selon le brief 9.5, fiches des 10 produits, zones et tarifs de livraison, textes légaux, comptes Meta, paiement et hébergement.
