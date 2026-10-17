# AMANI Cosmetics

Boutique en ligne d'AMANI Cosmetics (ALYA Group), à Dakar : vitrine, espace cliente, administration, espace livreur, paiements Wave et Orange Money, livraison au Sénégal.

Le cahier des charges et les maquettes du thème choisi sont dans [`docs/`](docs/). Ce dépôt contient les **fondations et le système de design** : tout ce qui sert ensuite à construire la boutique, le panier, la commande et la livraison.

## Ce qui est en place

| Brique | Où |
|---|---|
| Laravel 12, MySQL 8, Redis, files de travaux (Horizon sur `/administration/files`) | `config/`, `bootstrap/` |
| Compte cliente par téléphone sénégalais et code SMS : code à 6 chiffres valable 10 minutes, usage unique, envois limités, verrouillage 15 minutes après 5 codes faux, création du compte au premier passage | `app/Http/Controllers/Comptes/`, `app/Support/Comptes/`, `resources/views/comptes/` |
| Espace cliente (`/fr/mon-compte`) : accueil, menu latéral, modification du profil (le numéro de téléphone ne change pas depuis le profil) | `resources/views/components/layouts/espace-client.blade.php`, `app/Http/Controllers/Comptes/ProfilController.php` |
| Administration (`/administration`) : double authentification obligatoire, rôles propriétaire / administrateur / livreur, permissions fines, gestion du personnel, tableau de bord avec actions rapides, menu latéral sombre de la maquette | `app/Filament/`, `app/Support/Acces/` |
| Journal des actions sensibles, impossible à modifier ou supprimer (même en base) | `app/Models/JournalActivite.php` |
| Chaîne d'images : chaque photo déclinée en 5 largeurs (400 à 2400 px), en AVIF et en WebP | `app/Support/Medias/DeclinaisonsImages.php` |
| Français au lancement, anglais prêt mais fermé (`BOUTIQUE_LANGUES`) | `app/Support/Langues.php`, `lang/` |
| Système de design AMANI d'après les maquettes : couleurs, polices auto-hébergées, 38 composants Blade | `resources/css/amani/`, `resources/views/components/amani/`, page `/systeme-de-design` |
| 91 tests automatiques, rejoués sur GitHub à chaque envoi | `tests/`, `.github/workflows/tests.yml` |

## Installer le projet sur un poste de développement

Prérequis : PHP 8.3 avec les extensions `imagick`, `intl`, `pdo_mysql`, `redis`, `zip` ; Composer ; Node 22 ; MySQL 8 ; Redis.
Sur Ubuntu 24.04, l'écriture des images AVIF demande en plus le paquet `libheif-plugin-aomenc`.

```bash
composer setup                       # dépendances, .env, clé, base, rôles, lien de stockage, compilation
php artisan amani:creer-personnel    # crée le compte de la propriétaire (mot de passe saisi à la main)
composer dev                         # serveur, files de travaux, journaux et Vite en même temps
```

Puis ouvrir http://localhost:8000 (boutique), http://localhost:8000/administration (administration) et http://localhost:8000/systeme-de-design (bibliothèque de composants).

**Se connecter comme cliente en développement** : tant qu'aucun prestataire SMS n'est choisi, le code n'est pas envoyé mais écrit dans `storage/logs/laravel.log` (« votre code de connexion est … »).

### Réglage MySQL indispensable

Le journal est protégé par des déclencheurs en base. Si MySQL refuse de les créer (« You do not have the SUPER privilege and binary logging is enabled »), lancer une fois, en administrateur MySQL :

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
| `recette` | Validation par AMANI avant chaque mise en production. La page `/systeme-de-design` y est ouverte. |
| `production` | Le site public. Le SMS de démonstration y est refusé. |

Le déploiement (serveur, nom de domaine, sauvegardes) sera branché quand l'hébergement sera choisi (cahier §16).

## Ce qu'AMANI doit fournir pour la suite

Voir le cahier §21 : points bloquants (retenue de 10 %, retours 24 h / réclamations 7 jours, solution des réclamations), format du numéro de commande, barème VIP et valeur des points, zones et tarifs de livraison, catalogue complet avec photos, données légales, logo officiel (lotus ou couronne), prestataire SMS, comptes Wave et Orange Money.
