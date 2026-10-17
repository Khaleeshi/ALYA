<?php

namespace App\Support\Acces;

/**
 * Permissions fines du personnel. La dirigeante les a toutes (voir
 * AppServiceProvider) ; les autres rôles reçoivent celles de PAR_ROLE, et la
 * dirigeante peut en accorder une de plus à une personne précise
 * (par exemple l'espace achats à un administrateur, §42).
 */
final class Permissions
{
    public const CATALOGUE_GERER = 'catalogue.gerer';

    public const COMMANDES_GERER = 'commandes.gerer';

    public const LIVRAISONS_GERER = 'livraisons.gerer';

    public const LIVRAISONS_EFFECTUER = 'livraisons.effectuer';

    public const CLIENTES_VOIR = 'clientes.voir';

    public const APRES_VENTE_GERER = 'apres-vente.gerer';

    public const CONTENUS_GERER = 'contenus.gerer';

    public const BILANS_VOIR = 'bilans.voir';

    public const ACHATS_GERER = 'achats.gerer';

    public const REMBOURSEMENTS_EXECUTER = 'remboursements.executer';

    public const JOURNAL_VOIR = 'journal.voir';

    public const UTILISATEURS_GERER = 'utilisateurs.gerer';

    public const REGLAGES_GERER = 'reglages.gerer';

    public const TECHNIQUE_SUPERVISER = 'technique.superviser';

    /**
     * @return array<string, string>
     */
    public static function libelles(): array
    {
        return [
            self::CATALOGUE_GERER => 'Gérer le catalogue (produits, collections, promotions)',
            self::COMMANDES_GERER => 'Gérer les commandes',
            self::LIVRAISONS_GERER => 'Organiser les livraisons (zones, créneaux, lots)',
            self::LIVRAISONS_EFFECTUER => 'Effectuer des livraisons (espace livreur)',
            self::CLIENTES_VOIR => 'Consulter les clientes',
            self::APRES_VENTE_GERER => 'Traiter retours, échanges, réclamations et avis',
            self::CONTENUS_GERER => 'Gérer les contenus du site',
            self::BILANS_VOIR => 'Consulter les bilans',
            self::ACHATS_GERER => 'Espace achats',
            self::REMBOURSEMENTS_EXECUTER => 'Exécuter les remboursements',
            self::JOURNAL_VOIR => "Consulter le journal d'activité",
            self::UTILISATEURS_GERER => 'Gérer le personnel et les accès',
            self::REGLAGES_GERER => 'Modifier les réglages',
            self::TECHNIQUE_SUPERVISER => 'Superviser les files de travaux',
        ];
    }

    /**
     * @return list<string>
     */
    public static function toutes(): array
    {
        return array_keys(self::libelles());
    }

    /**
     * Permissions données par défaut à chaque rôle. La dirigeante n'apparaît
     * pas : elle a tout.
     *
     * @var array<string, list<string>>
     */
    public const PAR_ROLE = [
        Roles::ADMINISTRATEUR => [
            self::CATALOGUE_GERER,
            self::COMMANDES_GERER,
            self::LIVRAISONS_GERER,
            self::CLIENTES_VOIR,
            self::APRES_VENTE_GERER,
            self::CONTENUS_GERER,
            self::BILANS_VOIR,
            self::JOURNAL_VOIR,
        ],
        Roles::LIVREUR => [
            self::LIVRAISONS_EFFECTUER,
        ],
    ];
}
