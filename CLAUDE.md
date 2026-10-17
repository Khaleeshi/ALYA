# ALYA Cosmetic

Boutique en ligne premium (noir profond + or champagne) pour ALYA Cosmetic, à Dakar : vitrine, espace cliente, administration, espace livreur, paiements Wave et Orange Money, notifications WhatsApp. Le projet technique est porté par Nogaye (compte GitHub Khaleeshi), qui lit le français : tout lui est expliqué en français, en phrases simples, sans jargon.

## Documents de référence

- `CAHIER DES CHARGES COMPLET — ALYA C.txt` : les 63 sections écrites par la cliente ALYA. « CDC §n » désigne la section n.
- `AUDIT ALYA COSMETIC - 2026-09-07.html` : lecture critique du cahier des charges et des maquettes. Il tranche les règles métier (section 6), l'architecture (section 7), le plan en huit phases (section 8), la direction artistique et le système de design (section 9), les exigences mesurables (10) et la recette (11). Les seize décisions D1 à D16 de la section 5 appartiennent à la direction ALYA : tant qu'elle n'a pas tranché autrement, appliquer la recommandation de l'audit.
- `PHOTO-*.jpg` : les dix maquettes auditées (la section 9.4 de l'audit les désigne par leur nom, « Fichier 22 (3) » = `PHOTO-2026-08-09-13-31-22 (3).jpg`). Elles servent à lister les contenus voulus, jamais à être copiées : leur rendu est banni par la section 9.2.

Ordre de priorité en cas de contradiction : l'audit, puis le cahier des charges, puis les maquettes.

## Décisions déjà prises

- La marque s'écrit ALYA (le dossier s'appelle AYLA par erreur de frappe).
- Pile : Laravel 12, PHP 8.3, MySQL 8, Redis, vitrine Blade + Tailwind CSS 4 + Alpine.js, Livewire 3, back-office Filament 4, espace livreur en PWA (audit section 7). Un seul dépôt, un seul déploiement.
- Tables et code métier nommés en français, comme la section 7.4 de l'audit.
- Fuseau Africa/Dakar, FCFA (XOF) comme seule monnaie de paiement, français par défaut et anglais complet.

## Règles de travail

- Système de design de la section 9.3 : palette fixée (noir profond #070707, or champagne #C8A95A, ivoire #F3EDE2…), Bodoni Moda pour les titres et les prix, Jost pour le texte, angles vifs, aucune ombre portée, un seul bouton doré plein par écran, aucune particule en CSS, aucun émoji dans l'interface, aucun chiffre inventé.
- La règle fondamentale du CDC §62 ne se discute pas : pas de numéro de commande ni de sortie de stock avant la confirmation réelle du paiement.
- Jamais de secret dans le code ; `.env` est ignoré ; `.env.example` documente chaque variable.
- Messages de commit en français, une phrase qui dit ce que la personne obtient.
