♛

AMANI

**COSMETICS**

**CAHIER DES CHARGES FONCTIONNEL, TECHNIQUE ET COMMERCIAL**

Conception et développement du site e-commerce

Version 1.0 --- 17 octobre 2026\
Signature de marque : « Parce que vous méritez tous de briller. »

# 1. Présentation du projet

Le projet consiste à concevoir et développer le site e-commerce officiel
d'AMANI Cosmetics, première marque beauté d'ALYA Group. Le site doit
présenter les produits, permettre aux clientes de commander en ligne,
payer via les moyens disponibles, choisir une livraison au Sénégal,
suivre leurs commandes et accéder à un espace client. Il doit également
fournir une interface professionnelle complète pour gérer l'activité.

Le présent document formalise les décisions prises pendant la
préparation du projet. Les éléments explicitement signalés comme « à
valider » ne doivent pas être considérés comme des règles finales avant
confirmation.

## 1.1 Objectifs

-   Créer une boutique en ligne élégante, claire et rassurante, adaptée
    en priorité au mobile.

-   Présenter les catégories Gloss, Skincare et Maquillage, les
    collections, les éditions limitées et les coffrets cadeaux.

-   Permettre une commande suivie de bout en bout : panier, livraison,
    paiement, confirmation et suivi.

-   Automatiser la gestion du catalogue, du stock, des promotions, des
    commandes, des retours et des réclamations.

-   Fournir des espaces distincts et sécurisés pour les clientes,
    l'administration et les livreurs.

-   Préparer une architecture évolutive pouvant accueillir de nouvelles
    gammes et fonctionnalités.

## 1.2 Périmètre de lancement

-   Marché initial : Sénégal uniquement.

-   Devise officielle de paiement : FCFA (XOF). D'autres devises peuvent
    éventuellement être affichées à titre indicatif, avec conversion
    clairement présentée.

-   Moyens de paiement prévus : Wave et Orange Money, sous réserve de
    l'intégration technique et de l'activation des comptes marchands.

-   Langue initiale du site : français. Une version anglaise n'est pas
    confirmée pour cette phase.

-   Livraison nationale par zones, avec tarifs et créneaux configurables
    dans l'administration.

# 2. Identité de marque et direction artistique

## 2.1 Identité

  -----------------------------------------------------------------------
  **Élément**                         **Spécification**
  ----------------------------------- -----------------------------------
  Nom de marque                       AMANI Cosmetics

  Signature                           « Parce que vous méritez tous de
                                      briller. »

  Emblème                             Fleur de lotus dorée, avec une
                                      présentation typographique premium.
                                      Les maquettes utilisent également
                                      une couronne décorative : l'emblème
                                      final doit être confirmé.

  Positionnement visuel               Beauté premium, élégante,
                                      chaleureuse et accessible

  Ambiance                            Nude enveloppant, lumineux, soigné,
                                      haut de gamme
  -----------------------------------------------------------------------

## 2.2 Palette de couleurs proposée

  -----------------------------------------------------------------------
  **Usage**               **Couleur**             **Code proposé**
  ----------------------- ----------------------- -----------------------
  Fond principal          Beige nude              #E8D5BE

  Accents et boutons      Or élégant              #C6A15B

  Fond clair              Crème                   #F8F3EC

  Texte principal         Brun profond            #493729
  -----------------------------------------------------------------------

Les codes sont une base de travail à valider sur les maquettes finales.
Les boutons principaux sont dorés ; les boutons secondaires utilisent
des contours nude ou bruns. La typographie associe une police à
empattements élégante pour les titres et une police sans empattements
très lisible pour le texte.

## 2.3 Photographie et composants

-   Photographies produits de style studio : fond nude, lumière douce,
    reflets maîtrisés et ombres délicates.

-   Cartes produits cohérentes, coins légèrement arrondis, espaces
    généreux et détails dorés discrets.

-   Pas de musique lancée automatiquement.

-   Animations discrètes, non intrusives et compatibles avec les
    appareils mobiles.

-   Les maquettes générées sont des références visuelles, pas des pages
    fonctionnelles prêtes à publier.

# 3. Arborescence du site

  -----------------------------------------------------------------------
  **N°**                  **Page / espace**       **Contenu principal**
  ----------------------- ----------------------- -----------------------
  1                       Accueil                 Présentation de marque,
                                                  catégories, nouveautés,
                                                  meilleures ventes,
                                                  promotions

  2                       Boutique                Catalogue, recherche,
                                                  tri, filtres

  3                       Fiche produit           Galerie, prix,
                                                  variantes,
                                                  disponibilité,
                                                  description,
                                                  ingrédients, conseils,
                                                  avis

  4                       Collections             Collections permanentes
                                                  et saisonnières

  5                       Éditions limitées       Produits limités,
                                                  périodes et stocks
                                                  disponibles

  6                       Coffrets cadeaux        Coffrets prédéfinis,
                                                  emballage cadeau payant
                                                  et message personnalisé

  7                       Guide beauté            Routines complètes et
                                                  recommandations de
                                                  produits

  8                       Panier                  Articles, quantités,
                                                  codes promotionnels,
                                                  sous-total et
                                                  estimation de livraison

  9                       Connexion / création de Accès client par
                          compte                  téléphone et code OTP
                                                  SMS

  10                      Livraison               Coordonnées, zone,
                                                  adresse et créneau
                                                  disponible

  11                      Paiement                Wave ou Orange Money

  12                      Confirmation            Numéro de commande et
                                                  récapitulatif

  13                      Suivi de commande       Recherche par numéro et
                                                  vérification d'identité
                                                  appropriée

  14                      Précommandes            Produits permanents
                                                  temporairement
                                                  indisponibles et règles
                                                  applicables

  15                      Tableau de bord client  Résumé des commandes,
                                                  points, favoris et
                                                  informations
                                                  personnelles

  16                      Mes commandes           Historique et détails

  17                      Mes favoris             Produits sauvegardés

  18                      Mes points fidélité     Solde, historique,
                                                  expiration et
                                                  récompenses

  19                      Mes retours et          Formulaires, pièces
                          réclamations            jointes et suivi de
                                                  statut

  20                      Mes avis                Avis déposés après
                                                  réception d'une
                                                  commande

  21                      Mon profil              Téléphone, coordonnées
                                                  et adresses

  22                      À propos                Histoire, vision,
                                                  mission et valeurs

  23                      FAQ                     Questions classées par
                                                  thème et recherche si
                                                  retenue

  24                      Contact                 Formulaire, WhatsApp et
                                                  coordonnées officielles

  25                      Collaborations          Formulaire de demande
                                                  de partenariat

  26                      Politique de livraison  Zones, créneaux, frais,
                                                  livraison impossible

  27                      Retours, échanges,      Conditions, délais,
                          remboursements et       preuves et traitement
                          réclamations            

  28                      Conditions générales    Utilisation du site et
                                                  règles générales

  29                      Confidentialité         Données personnelles,
                                                  finalités, sécurité et
                                                  droits

  30                      Commande, paiement et   Validation, moyens de
                          annulation              paiement, annulation,
                                                  précommandes

  31                      Administration          Gestion commerciale et
                                                  technique du site

  32                      Espace livreur          Livraisons attribuées
                                                  et mise à jour des
                                                  statuts
  -----------------------------------------------------------------------

Les 32 éléments représentent des pages ou espaces fonctionnels.
Certaines pages peuvent partager un même modèle technique.

# 4. En-tête, navigation et pied de page

## 4.1 En-tête

-   Bandeau de service : livraison au Sénégal, paiement sécurisé et
    liens FAQ, Contact, Suivi de commande.

-   Logo AMANI Cosmetics.

-   Menu principal : Accueil, Boutique, Collections, Éditions limitées,
    Coffrets cadeaux, Guide beauté, À propos.

-   Icônes : recherche, compte client, favoris et panier avec compteur
    d'articles.

-   Devise affichée : FCFA. Toute conversion éventuelle est indicative ;
    le paiement reste en FCFA.

## 4.2 Pied de page

-   Signature de marque et liens vers les principales catégories.

-   Liens d'aide : FAQ, livraison, retours et remboursements, conditions
    générales, confidentialité et mentions légales.

-   Contact et liens vers WhatsApp, Instagram et TikTok.

-   Inscription newsletter uniquement après définition du consentement
    et du processus de désinscription.

# 5. Accueil et catalogue

## 5.1 Page d'accueil

-   Bannière principale axée sur la marque, le lotus/logo, la signature
    et un bouton vers la collection.

-   Accès rapide aux catégories Gloss, Skincare et Maquillage.

-   Bandeau promotionnel élégant unique en haut de page, sans pop-up.

-   Meilleures ventes calculées automatiquement à partir des ventes
    réelles.

-   Section nouveautés alimentée automatiquement, avec possibilité
    d'ajustement manuel dans l'administration.

-   Collections, coffrets et promotions actives.

-   Les promotions avec compte à rebours ne sont affichées que pour des
    offres réellement limitées dans le temps.

## 5.2 Boutique et filtres

-   Grille de quatre produits par ligne sur grand écran, adaptée aux
    tablettes et mobiles.

-   Recherche par nom et mots-clés.

-   Tri : nouveautés, prix croissant/décroissant et popularité, selon
    données disponibles.

-   Filtres personnalisés par catégorie : prix, disponibilité,
    permanent/édition limitée, effet/besoin et ingrédients pertinents.

-   Pas de fonctionnalité de comparaison de produits au lancement.

## 5.3 Fiche produit

-   Photos multiples et, si prévu, courte vidéo du produit.

-   Nom, prix FCFA, statut de stock, variantes et quantité.

-   Description, bénéfices, ingrédients, conseils d'utilisation et
    informations de livraison.

-   Avis vérifiés après achat livré.

-   Produits similaires ou complémentaires.

-   Boutons Ajouter au panier et Acheter maintenant.

## 5.4 Produits et données

Le catalogue initial et les quantités définitives doivent être fournis
par AMANI. Chaque fiche produit doit pouvoir contenir : identifiant,
nom, catégorie, description, prix, coût interne facultatif, photos,
variantes, ingrédients, conseils, stock, seuil d'alerte, statut
permanent/édition limitée, dates de campagne éventuelles et statut de
publication.

# 6. Panier, commande et paiement

## 6.1 Panier

-   Modifier les quantités, retirer un article et enregistrer un favori.

-   Afficher sous-total, réduction, frais de livraison et total final
    séparément.

-   Champ de code promotionnel et messages d'erreur explicites.

-   Estimation des frais de livraison selon la zone lorsqu'elle est
    renseignée.

-   Un article placé au panier ne réserve pas automatiquement le stock.

## 6.2 Compte et identification

Décision retenue dans les échanges récents : le compte client est
obligatoire pour commander. L'accès prévu utilise le numéro de téléphone
et un code à usage unique (OTP) envoyé par SMS. Cette fonction exige un
prestataire SMS, une gestion de l'expiration et des limites de
tentatives. La récupération d'accès doit être sécurisée.

## 6.3 Parcours de commande

1.  La cliente consulte son panier et confirme les quantités.

2.  Elle renseigne ses coordonnées, son adresse, sa zone et un créneau
    disponible.

3.  Le site calcule les frais de livraison et affiche le récapitulatif
    complet avant paiement.

4.  La cliente choisit Wave ou Orange Money et suit le parcours sécurisé
    du prestataire.

5.  Le serveur vérifie la confirmation réelle du paiement, idéalement
    via notification serveur/webhook signé et vérification auprès du
    prestataire.

6.  Uniquement après confirmation du paiement, le système crée la
    commande définitive, attribue le numéro, applique les règles de
    stock, enregistre les avantages et affiche la confirmation.

Un paiement échoué, interrompu, annulé ou non confirmé ne doit pas créer
une commande payée ni provoquer une décrémentation définitive du stock.
Les éventuelles réservations temporaires doivent expirer
automatiquement.

## 6.4 Numéro de commande et statuts

-   Numéro unique créé après confirmation du paiement. La forme exacte
    est à définir : les documents évoquent à la fois une séquence 001,
    002... et des références de type #AMN10234.

-   Les commandes restent dans l'historique, y compris lorsqu'elles sont
    annulées.

-   Statuts de livraison de base : Reçu → En attente de livraison →
    Livrée.

-   Des statuts internes supplémentaires sont nécessaires pour
    préparation, paiement, annulation, retour et remboursement.

## 6.5 Modifications et annulations

-   Une commande payée ne peut pas être modifiée directement par la
    cliente : elle contacte le service client, qui vérifie si la
    préparation permet encore un changement.

-   Annulation en ligne autorisée depuis le compte avant le début de la
    préparation ; après préparation, la cliente contacte le service
    client.

-   Le document commercial proposé prévoit une retenue de 10 % en cas
    d'annulation après paiement. Cette règle reste à confirmer par la
    propriétaire et doit être vérifiée juridiquement avant publication.

-   Lorsqu'une annulation est acceptée, la commande garde son historique
    et le stock est réintégré selon des règles contrôlées.

# 7. Paiements, prix, promotions et précommandes

## 7.1 Paiements

-   Moyens prévus : Wave et Orange Money.

-   Devise de paiement : FCFA.

-   Les identifiants marchands, les environnements de test, les clés
    API, les notifications et les procédures de remboursement doivent
    être configurés avec les prestataires.

-   Aucune commande ne doit être marquée payée sur la seule base d'une
    capture d'écran ou d'une redirection navigateur.

## 7.2 Promotions

-   Codes promotionnels, offres à durée limitée, bundles, cadeaux
    au-dessus d'un seuil et campagnes par catégorie.

-   L'administration définit dates, produits éligibles, limites
    d'utilisation, conditions de cumul et stock des cadeaux.

-   Règle du document commercial : une seule réduction/offre par
    commande, sauf exception explicitement paramétrée.

-   Les frais de livraison ne sont pas réduits par les promotions, sauf
    décision contraire affichée clairement.

## 7.3 Précommandes

-   Disponibles uniquement pour les produits permanents temporairement
    en rupture de stock.

-   Réduction prévue de 10 % sur les précommandes.

-   Précommandes enregistrées séparément et prioritaires lors du
    réapprovisionnement.

-   À la réception du stock, les quantités nécessaires aux précommandes
    sont réservées avant la remise en vente du reliquat.

-   Les éditions limitées en rupture ne sont pas nécessairement
    précommandables.

-   Les délais estimés, conditions d'annulation et modalités de
    remboursement des précommandes doivent être affichés avant paiement.

# 8. Programme VIP et fidélité

## 8.1 Cadeaux VIP

  -----------------------------------------------------------------------
  **Montant de commande prévu**       **Avantage annoncé dans la
                                      politique**
  ----------------------------------- -----------------------------------
  50 000 à moins de 75 000 FCFA       1 gloss VIP

  75 000 à moins de 100 000 FCFA      2 gloss VIP

  100 000 FCFA et plus                3 gloss VIP ou davantage selon les
                                      paliers définis
  -----------------------------------------------------------------------

Les cadeaux VIP ne sont pas vendus et dépendent du stock. Le document
fourni indique que le montant pris en compte comprend la livraison et
qu'un choix de cadeau peut être proposé, avec sélection automatique
possible si aucun choix n'est fait. Cette règle doit être confirmée.

## 8.2 Programme de fidélité par points

  -----------------------------------------------------------------------
  **Règle**                           **Décision actuelle**
  ----------------------------------- -----------------------------------
  Acquisition                         1 point par tranche de 1 000 FCFA
                                      dépensés

  Base de calcul                      Montant des produits réellement
                                      payé après réductions, hors frais
                                      de livraison

  Crédit des points                   Après livraison de la commande

  Premier seuil                       50 points, soit environ 50 000 FCFA
                                      de dépenses éligibles

  Récompense                          La cliente choisit entre une
                                      réduction et un produit offert

  Expiration                          Après 12 mois d'inactivité

  Annulation/remboursement            Les points liés à la commande
                                      annulée ou remboursée sont retirés

  Utilisation                         Choix manuel au paiement, pas de
                                      conversion automatique

  Historique                          Points gagnés par commande,
                                      utilisés, corrections et
                                      expirations visibles dans le compte

  Cumul                               Points et promotions cumulables
                                      selon la décision actuelle ; calcul
                                      sur le montant payé éligible
  -----------------------------------------------------------------------

À définir avant développement : valeur FCFA de la récompense en
réduction, liste et valeur des produits offerts, règles en cas de
remboursement partiel et articulation entre cadeaux VIP et points de
fidélité.

# 9. Livraison au Sénégal

## 9.1 Zones et créneaux

-   Livraison au Sénégal uniquement au lancement.

-   Les zones, frais et créneaux sont administrables par zone.

-   La cliente choisit une zone et un créneau disponible pendant la
    commande.

-   Le samedi est autorisé selon les disponibilités configurées.

-   Le dimanche, l'activité de livraison est fermée.

-   Les créneaux affichés doivent tenir compte du planning, de la zone
    et de la capacité des livreurs.

## 9.2 Livraison impossible et retard

-   La cliente doit fournir un numéro joignable et une adresse avec des
    indications suffisantes.

-   En cas d'absence, de cliente injoignable ou d'impossibilité de
    livraison, la commande peut être reprogrammée au lendemain selon les
    disponibilités.

-   Aucun nouveau frais de livraison n'est appliqué pour une
    reprogrammation relevant de la procédure prévue.

-   En cas de retard important, l'équipe peut informer la cliente et
    proposer un nouveau délai.

-   La date et l'heure de livraison sont enregistrées lorsque la
    commande est marquée Livrée.

## 9.3 Espace livreur

-   Connexion individuelle et accès uniquement aux livraisons
    attribuées.

-   Affichage des informations nécessaires à la livraison, sans accès à
    l'ensemble des données commerciales.

-   Consultation de l'adresse, du téléphone, du créneau et des consignes
    utiles.

-   Mise à jour des statuts et enregistrement du résultat de la
    livraison.

-   Gestion d'un échec de livraison avec motif et proposition de
    reprogrammation.

# 10. Retours, échanges, remboursements et réclamations

## 10.1 Délais définis dans la politique fournie

  -----------------------------------------------------------------------
  **Procédure**                       **Délai de la politique**
  ----------------------------------- -----------------------------------
  Demande de retour                   24 heures après que la commande est
                                      officiellement marquée LIVRÉE

  Réclamation                         7 jours après que la commande est
                                      officiellement marquée LIVRÉE
  -----------------------------------------------------------------------

## 10.2 Formulaires

-   Le compte client contient un formulaire rattaché à la commande
    concernée.

-   Champs de réclamation : nom, prénom, téléphone, numéro de commande,
    type, description et pièces jointes.

-   Motifs de retour : produit défectueux, mauvais produit, produit
    endommagé ou autre motif.

-   Types de réclamation : produit, remboursement, échange ou autre.

-   Possibilité de joindre photos et vidéos. Les fichiers doivent être
    limités en taille/type, contrôlés et stockés de manière sécurisée.

## 10.3 Traitement administratif

-   À l'envoi, confirmation automatique à la cliente et création d'un
    dossier visible par l'administration.

-   Statuts de retour : En attente, Accepté, Refusé, Retour reçu.

-   Statuts de réclamation : Réclamation reçue, En cours, Résolue.

-   La cliente reçoit une notification lors des changements importants.
    Le canal exact (site, e-mail ou autre) doit être arrêté et
    configuré.

-   Le retour accepté doit être récupéré et vérifié avant tout
    remboursement lié à ce retour.

-   Après vérification, l'équipe consigne la décision et les motifs dans
    le dossier.

## 10.4 Solutions possibles et frais

-   Solutions envisagées : remplacement, remboursement total ou partiel,
    ou refus motivé lorsque légalement justifié.

-   La préférence exprimée dans les échanges est de laisser la cliente
    choisir entre remplacement et remboursement pour une réclamation
    acceptée, selon les conditions applicables et la disponibilité. La
    politique écrite permet aussi une décision au cas par cas : cette
    articulation doit être harmonisée.

-   Les frais de retour et de réexpédition sont déterminés selon la
    situation et consignés dans le dossier.

-   Un produit retourné n'est pas automatiquement remis en stock.

-   Les remboursements sont réalisés sur le moyen de paiement d'origine
    lorsque techniquement possible.

Important : les délais, exclusions, refus, retenues et règles de
remboursement doivent être vérifiés par rapport aux obligations légales
applicables au Sénégal. Une politique interne ne peut pas supprimer les
droits légaux des clientes.

# 11. Espace client

-   Tableau de bord avec commandes récentes, statuts et accès rapide aux
    fonctions principales.

-   Historique de commandes, détails, montants, dates et numéros.

-   Suivi de commande depuis le compte et page de suivi séparée avec
    numéro de commande et vérification d'identité appropriée.

-   Favoris synchronisés avec le compte.

-   Points fidélité : solde, historique, expiration et récompenses
    disponibles.

-   Formulaires et historique des retours/réclamations.

-   Avis publiables uniquement après une commande livrée, soumis à
    modération.

-   Gestion des coordonnées et adresses.

-   Possibilité de contacter le service client.

Les notifications prévues dans les décisions récentes sont
principalement affichées sur le site et dans le compte. Les
notifications automatiques par SMS ou WhatsApp ne sont pas prévues pour
l'instant, sauf confirmation ultérieure.

# 12. Administration

## 12.1 Tableau de bord

-   Chiffre d'affaires, commandes, clients, produits vendus et évolution
    sur une période sélectionnable.

-   Commandes récentes et alertes de stock faible.

-   Produits les plus vendus, répartition des ventes et nouveaux
    clients.

-   Messages clients, avis récents et retours/réclamations à traiter.

-   Actions rapides : ajouter un produit, créer une collection, créer
    une promotion et préparer une campagne.

## 12.2 Gestion fonctionnelle

  -----------------------------------------------------------------------
  **Module**                          **Fonctions attendues**
  ----------------------------------- -----------------------------------
  Commandes                           Recherche, détail, statut,
                                      paiement, annulation, historique,
                                      livraison et remboursement

  Produits                            Création, édition, variantes,
                                      photos, prix, catégories, stock et
                                      visibilité

  Collections                         Collections permanentes, éditions
                                      limitées, ordre et dates
                                      d'affichage

  Stock                               Décrémentation après confirmation
                                      de paiement selon la règle choisie,
                                      seuils d'alerte, blocage de vente à
                                      zéro et journal des mouvements

  Clients                             Profil, historique d'achat, points
                                      et demandes, accès limité selon
                                      rôle

  Promotions                          Codes, dates, réductions, cadeaux,
                                      bundles, limites, cumul et
                                      éligibilité

  VIP/fidélité                        Seuils, récompenses, mouvements de
                                      points et historique

  Livraison                           Zones, tarifs, créneaux, capacité,
                                      affectation et incidents

  Retours/réclamations                Dossiers, pièces jointes, décision,
                                      frais et historique

  Avis                                Validation, refus motivé et
                                      publication

  Messages/FAQ                        Messages de contact, questions et
                                      réponses

  Paramètres                          Comptes administrateurs, rôles,
                                      paiements, SMS, coordonnées,
                                      politiques et configuration du site
  -----------------------------------------------------------------------

## 12.3 Rôles et accès

-   Administrateur principal avec droits étendus et double
    authentification obligatoire.

-   Comptes professionnels nominatifs, avec droits limités au besoin.

-   Livreur séparé de l'administration et limité aux commandes qui lui
    sont attribuées.

-   Journal des actions sensibles : modification de prix, stock,
    remboursements, changements de statut et gestion des comptes.

# 13. Contact, FAQ, avis et collaborations

-   Formulaire de contact avec nom, adresse e-mail ou téléphone, sujet
    et message.

-   Coordonnées réelles, horaires et liens officiels à fournir avant
    publication ; aucune coordonnée de démonstration ne doit rester en
    production.

-   WhatsApp comme canal de contact, sans promettre de notifications
    automatiques si elles ne sont pas intégrées.

-   FAQ complète : commandes, paiement, livraison, produits, retours,
    remboursements, fidélité et compte client.

-   Avis vérifiés après commande livrée, avec note, commentaire et
    éventuellement photo/vidéo si la fonctionnalité est activée.

-   Le formulaire de collaboration envoie une demande sans garantir son
    acceptation.

-   Pas de programme de parrainage au lancement.

# 14. Coffrets et options cadeaux

-   Coffrets cadeaux prédéfinis disponibles dans une page dédiée.

-   Emballage cadeau premium proposé comme option payante.

-   Message cadeau personnalisé saisi par la cliente, sans supplément
    prévu pour le message.

-   Les cartes cadeaux sont reportées à une phase ultérieure.

# 15. Données personnelles et sécurité

-   Collecter uniquement les données nécessaires au compte, au paiement,
    à la livraison, aux avis et aux demandes de support.

-   Limiter les accès aux données selon le rôle et le besoin
    opérationnel.

-   Sécuriser les mots de passe/jetons, les sessions, les pièces jointes
    et les échanges réseau.

-   OTP : durée d'expiration, limitation des tentatives, protection
    contre les abus et journalisation adaptée.

-   Administration : double authentification du compte principal,
    comptes nominatifs et journal des actions.

-   Paiements : ne jamais stocker les données sensibles de paiement si
    le prestataire peut les gérer directement.

-   Sauvegardes régulières, procédure de restauration, mises à jour de
    sécurité et plan de gestion des incidents.

-   Politique de confidentialité précisant finalités, durées de
    conservation, prestataires, droits des personnes et contacts.

-   Vérification juridique des mentions légales, conditions générales et
    obligations de protection des données au Sénégal.

# 16. Exigences techniques et qualité

-   Site responsive et mobile-first, compatible avec les navigateurs
    modernes sur téléphone, tablette et ordinateur.

-   Pages optimisées pour le chargement : images compressées, formats
    adaptés, chargement différé et limitation des scripts inutiles.

-   Architecture permettant de faire évoluer les produits, catégories,
    zones et règles commerciales sans réécrire le site.

-   Moteur de recherche, navigation claire et URL compréhensibles.

-   Gestion des erreurs, pages 404, états de chargement et confirmations
    explicites.

-   Référencement de base : titres, descriptions, URLs, textes
    alternatifs des images, sitemap et métadonnées.

-   Accessibilité : contraste suffisant, champs étiquetés, navigation
    clavier et messages d'erreur compréhensibles.

-   Environnement de test distinct de la production, déploiement
    contrôlé et documentation technique.

La technologie, l'hébergement, le nom de domaine, le CMS/framework et
les outils de mesure restent à choisir par accord avec le développeur.
Les choix doivent être documentés et tenir compte du coût de
maintenance.

# 17. Intégrations externes nécessaires

  -----------------------------------------------------------------------
  **Intégration**         **Utilité**             **À prévoir**
  ----------------------- ----------------------- -----------------------
  Wave                    Paiement et, si         Compte marchand, API,
                          disponible,             tests, webhooks et
                          remboursement           rapprochement

  Orange Money            Paiement et, si         Compte marchand, API,
                          disponible,             tests, webhooks et
                          remboursement           rapprochement

  SMS/OTP                 Connexion par téléphone Prestataire, coûts,
                                                  délivrabilité et
                                                  protection contre les
                                                  abus

  E-mail                  Confirmation et         Service d'envoi,
                          messages de service si  authentification du
                          retenus                 domaine et modèles

  WhatsApp                Contact client          Lien officiel et numéro
                                                  validé ; automatisation
                                                  non incluse sans
                                                  intégration distincte

  Analytique              Mesure de fréquentation Outil à sélectionner,
                          et de conversion        consentement si
                                                  nécessaire et
                                                  minimisation des
                                                  données
  -----------------------------------------------------------------------

La présence d'un bouton ou d'une maquette ne signifie pas que
l'intégration est active. Chaque service doit être configuré, testé et
validé avant la mise en ligne.

# 18. Politiques commerciales à publier

-   Politique de livraison : Sénégal, tarifs par zone, créneaux
    disponibles, samedi selon disponibilités, dimanche fermé,
    reprogrammation et suivi.

-   Politique de retours, échanges, remboursements et réclamations :
    retour sous 24 heures, réclamation sous 7 jours à partir du statut
    Livrée, justificatifs et étapes de traitement.

-   Politique de commande, paiement et annulation : commande définitive
    après confirmation réelle du paiement, moyens de paiement,
    promotions, précommandes et annulations.

-   Conditions générales d'utilisation/vente selon le périmètre
    juridique validé.

-   Politique de confidentialité et traitement des données.

-   Mentions légales avec identité et coordonnées officielles de
    l'entreprise.

Les textes commerciaux transmis sous le nom ALYA Cosmetic doivent être
adaptés à AMANI Cosmetics avant publication. Les éléments juridiques
doivent être vérifiés par une personne compétente au regard du droit
sénégalais.

# 19. Critères de recette et d'acceptation

7.  Le site s'affiche correctement sur mobile, tablette et ordinateur.

8.  Les pages et menus de l'arborescence sont accessibles et cohérents.

9.  Les filtres, recherches, variantes, favoris et panier fonctionnent
    comme prévu.

10. Une commande n'est créée comme définitive qu'après confirmation
    vérifiée du paiement.

11. Un paiement échoué ou non confirmé ne déclenche pas une vente
    définitive ni une décrémentation définitive du stock.

12. Les frais de livraison correspondent à la zone et au créneau
    sélectionnés.

13. Les stocks, alertes et blocages de rupture suivent les règles
    configurées.

14. Les promotions respectent les conditions de cumul et les dates
    définies.

15. Les points de fidélité sont calculés, crédités après livraison et
    corrigés en cas d'annulation/remboursement selon les règles
    validées.

16. Les clientes peuvent suivre leurs commandes et déposer des
    retours/réclamations dans les délais configurés.

17. L'administration peut traiter commandes, stocks, produits, clients,
    promotions, avis et dossiers de support.

18. Un livreur ne voit que les livraisons qui lui sont attribuées.

19. La double authentification de l'administrateur principal est active.

20. Les notifications, confirmations, e-mails/SMS retenus et erreurs
    sont testés.

21. Les pages légales, contacts, prix, frais, conditions et mentions de
    paiement sont corrects avant mise en production.

# 20. Livrables attendus du prestataire

-   Maquettes validées des principales pages desktop et mobile.

-   Site développé et configuré sur un environnement de test.

-   Intégrations de paiement, OTP et notifications prévues dans le
    périmètre.

-   Interface d'administration et espace livreur.

-   Tests fonctionnels, sécurité de base et corrections de recette.

-   Documentation d'utilisation pour l'administration et les livreurs.

-   Documentation technique et inventaire des services tiers utilisés.

-   Accès propriétaire au nom de domaine, hébergement, comptes
    marchands, comptes d'envoi et dépôt de code si applicable.

-   Procédure de sauvegarde, restauration, maintenance et gestion des
    incidents.

-   Mise en production après validation écrite.

# 21. Points à arbitrer avant le développement final

  ----------------------------------------------------------------------------
  **Priorité**            **Point à trancher**    **Pourquoi c'est important**
  ----------------------- ----------------------- ----------------------------
  Bloquant                Règle d'annulation et   La retenue est proposée dans
                          retenue de 10 %         le texte, mais n'a pas été
                                                  confirmée et doit être
                                                  juridiquement vérifiée.

  Bloquant                Articulation retour 24  Définir la différence entre
                          h / réclamation 7 jours retour commercial et
                                                  réclamation pour défaut,
                                                  erreur ou dommage, sans
                                                  réduire les droits légaux.

  Bloquant                Solution d'une          Harmoniser le choix cliente
                          réclamation acceptée    entre
                                                  remplacement/remboursement
                                                  avec les possibilités de
                                                  remboursement partiel et les
                                                  règles applicables.

  Bloquant                Compte obligatoire      Décision récente : compte
                                                  obligatoire ; le texte plus
                                                  ancien autorise la commande
                                                  sans compte. Le présent
                                                  cahier retient le compte
                                                  obligatoire.

  Important               Numérotation des        Choisir un format unique :
                          commandes               séquence simple ou référence
                                                  de type #AMN10234.

  Important               Programme VIP et        Confirmer si les cadeaux VIP
                          fidélité                sont maintenus en plus des
                                                  points, et fixer le barème
                                                  exact à partir de 100 000
                                                  FCFA.

  Important               Base du seuil VIP       Confirmer si le seuil est
                                                  calculé livraison comprise,
                                                  alors que les points sont
                                                  calculés hors livraison.

  Important               Valeur des points       Définir la valeur de la
                                                  réduction et les produits
                                                  disponibles comme
                                                  récompenses.

  Important               Notifications           Confirmer le canal de
                                                  notification pour les
                                                  réclamations et commandes :
                                                  compte uniquement ou e-mail
                                                  en plus.

  Important               Créneaux et zones       Fournir la liste officielle
                                                  des zones, tarifs, jours et
                                                  capacités de livraison.

  Important               Catalogue               Fournir la liste finale des
                                                  produits, variantes, prix,
                                                  ingrédients, photos et
                                                  stocks.

  Important               Données légales         Fournir la dénomination
                                                  juridique, coordonnées,
                                                  contacts et informations
                                                  légales officielles.

  À planifier             FAQ avec barre de       La FAQ complète est retenue
                          recherche               ; la barre de recherche
                                                  reste à confirmer.

  À planifier             Langues                 Le français est retenu pour
                                                  le lancement ; une version
                                                  anglaise n'est pas confirmée
                                                  dans le périmètre actuel.
  ----------------------------------------------------------------------------

# 22. Proposition de phases de réalisation

  -----------------------------------------------------------------------
  **Phase**                           **Périmètre**
  ----------------------------------- -----------------------------------
  Phase 1 --- Cadrage                 Valider les points bloquants, les
                                      règles commerciales, le catalogue
                                      et les contenus légaux.

  Phase 2 --- UX/UI                   Finaliser les maquettes
                                      desktop/mobile, les composants et
                                      les parcours.

  Phase 3 --- Boutique                Accueil, catalogue, fiches
                                      produits, panier, compte et
                                      checkout.

  Phase 4 --- Opérations              Paiement, stock, commandes,
                                      livraison, espace client et espace
                                      livreur.

  Phase 5 --- Administration          Produits, promotions, fidélité,
                                      retours, réclamations, avis et
                                      statistiques.

  Phase 6 --- Tests                   Scénarios de paiement, erreurs,
                                      stock, rôles, mobile, sécurité et
                                      conformité des textes.

  Phase 7 --- Lancement               Formation, mise en production,
                                      suivi initial et plan de
                                      maintenance.
  -----------------------------------------------------------------------

# 23. Conclusion

Le site AMANI Cosmetics doit être une boutique premium, fiable et
administrable, avec une expérience mobile soignée et un parcours de
commande sécurisé. Le présent cahier des charges rassemble les décisions
actuellement connues et constitue une base de discussion avec le
développeur. Les points signalés comme à arbitrer doivent être résolus
avant la mise en production ; les paiements, SMS, données légales et
politiques doivent être réellement configurés et validés.

**AMANI COSMETICS\
Parce que vous méritez tous de briller.**
