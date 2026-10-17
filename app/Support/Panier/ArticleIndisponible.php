<?php

namespace App\Support\Panier;

use App\Models\Variante;
use RuntimeException;

/**
 * Variante qu'on ne peut plus mettre au panier : épuisée, retirée de la
 * vente, ou produit hors ligne.
 */
final class ArticleIndisponible extends RuntimeException
{
    public function __construct(public readonly Variante $variante)
    {
        parent::__construct(sprintf('« %s » n’est pas disponible.', $variante->libelle()));
    }
}
