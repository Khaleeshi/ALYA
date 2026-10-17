<?php

namespace App\Support\Acces;

/**
 * Rôles du personnel (garde « personnel »), cahier AMANI §12.3.
 */
final class Roles
{
    /** L'administrateur principal (la propriétaire d'AMANI) : voit et peut tout, y compris les achats et les remboursements. */
    public const PROPRIETAIRE = 'proprietaire';

    /** Compte professionnel nominatif : la gestion courante, sans les espaces réservés. */
    public const ADMINISTRATEUR = 'administrateur';

    /** Livreur : uniquement les livraisons qui lui sont attribuées, hors administration. */
    public const LIVREUR = 'livreur';

    public const GARDE = 'personnel';

    /**
     * @return array<string, string>
     */
    public static function libelles(): array
    {
        return [
            self::PROPRIETAIRE => 'Propriétaire',
            self::ADMINISTRATEUR => 'Administrateur',
            self::LIVREUR => 'Livreur',
        ];
    }

    /**
     * Rôles qui ouvrent l'administration.
     *
     * @return list<string>
     */
    public static function administration(): array
    {
        return [self::PROPRIETAIRE, self::ADMINISTRATEUR];
    }
}
