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

    'espace' => [
        'titre_menu' => 'Mon espace client',
        'membre_depuis' => 'Membre depuis le :date',
        'bienvenue' => 'Bienvenue',
        'accroche' => 'Votre espace client, tout simplement.',
        'merci' => 'Merci de faire partie de la famille :marque',
        'aide_titre' => 'Besoin d’aide ?',
        'aide_texte' => 'Notre équipe vous répond sur WhatsApp.',
        'aide_bouton' => 'Nous contacter',
        'menu' => [
            'tableau' => 'Tableau de bord',
            'commandes' => 'Mes commandes',
            'favoris' => 'Mes favoris',
            'points' => 'Mes points fidélité',
            'retours' => 'Mes retours et réclamations',
            'avis' => 'Mes avis',
            'profil' => 'Mon profil',
        ],
    ],

    'profil' => [
        'titre' => 'Mon profil',
        'modifier' => 'Modifier',
        'telephone_aide' => 'Ce numéro sert à vous connecter. Pour le changer, contactez le service client : le nouveau numéro sera vérifié par un code.',
        'enregistrer' => 'Enregistrer',
        'enregistre' => 'Vos informations sont enregistrées.',
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
