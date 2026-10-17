<?php

// Compte cliente : téléphone, code SMS, création du compte (cahier §6.2).
return [
    'connexion' => [
        'titre' => 'Connexion',
        'intro' => 'Entrez votre numéro de téléphone : nous vous envoyons un code par SMS. Première visite ? Votre compte se crée juste après.',
        'telephone' => 'Numéro de téléphone',
        'envoyer' => 'Recevoir mon code',
        'pourquoi' => 'Un compte est nécessaire pour commander : il garde vos commandes, vos favoris, vos points fidélité et vos demandes de retour.',
    ],

    'code' => [
        'titre' => 'Code de vérification',
        'intro' => 'Saisissez le code à 6 chiffres reçu par SMS au :telephone.',
        'code' => 'Code reçu par SMS',
        'se_souvenir' => 'Rester connectée sur cet appareil',
        'envoyer' => 'Valider',
        'renvoyer' => 'Renvoyer le code',
        'changer' => 'Changer de numéro',
        'envoye' => 'Un code vient d’être envoyé au :telephone. Il expire dans 10 minutes.',
        'invalide' => 'Ce code est incorrect ou a expiré. Vérifiez le SMS ou demandez un nouveau code.',
        'attendre' => 'Patientez :secondes secondes avant de demander un nouveau code.',
        'trop_envois' => 'Trop de codes demandés. Réessayez dans :minutes minutes.',
    ],

    'inscription' => [
        'titre' => 'Créer votre compte',
        'intro' => 'Votre numéro :telephone est vérifié. Encore quelques informations.',
        'prenom' => 'Prénom',
        'nom' => 'Nom',
        'email' => 'Email',
        'email_aide' => 'Pour recevoir aussi vos confirmations par email, si vous le souhaitez.',
        'messages_commande' => 'Le suivi de vos commandes s’affiche toujours dans votre compte.',
        'offres' => 'J’accepte de recevoir les offres et nouveautés AMANI.',
        'envoyer' => 'Créer mon compte',
    ],

    'mon_compte' => [
        'titre' => 'Mon compte',
        'bonjour' => 'Bonjour :prenom',
        'coordonnees' => 'Mes coordonnées',
        'telephone' => 'Téléphone',
        'email' => 'Email',
        'non_renseigne' => 'Non renseigné',
        'deconnexion' => 'Se déconnecter',
    ],

    'sms' => [
        'code' => 'AMANI Cosmetics : votre code de connexion est :code. Il expire dans :minutes minutes. Ne le communiquez à personne.',
    ],
];
