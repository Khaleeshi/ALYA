<?php

namespace App\Support\Acces;

/**
 * Rôles du personnel (garde « personnel »).
 */
final class Roles
{
    /** La PDG d'ALYA : voit et peut tout, y compris l'espace achats (§42) et les remboursements (D16). */
    public const DIRIGEANTE = 'dirigeante';

    /** Administrateur secondaire : la gestion courante, sans les espaces réservés. */
    public const ADMINISTRATEUR = 'administrateur';

    /** Livreur : son espace de livraison uniquement (phase 4). */
    public const LIVREUR = 'livreur';

    public const GARDE = 'personnel';

    /**
     * @return array<string, string>
     */
    public static function libelles(): array
    {
        return [
            self::DIRIGEANTE => 'Dirigeante',
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
        return [self::DIRIGEANTE, self::ADMINISTRATEUR];
    }
}
