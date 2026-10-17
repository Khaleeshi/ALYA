<?php

namespace App\Support\Catalogue;

use App\Models\Variante;
use RuntimeException;

/**
 * Retrait plus grand que le stock disponible : rien n'a été changé.
 */
final class StockInsuffisant extends RuntimeException
{
    public function __construct(public readonly Variante $variante, public readonly int $disponible, public readonly int $demande)
    {
        parent::__construct(sprintf('Stock insuffisant pour « %s » : %d disponible(s), %d demandé(s).', $variante->libelle(), $disponible, $demande));
    }
}
