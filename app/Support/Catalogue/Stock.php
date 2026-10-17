<?php

namespace App\Support\Catalogue;

use App\Models\JournalActivite;
use App\Models\MouvementStock;
use App\Models\Variante;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Seule porte d'entrée pour changer un stock. Chaque changement :
 * - verrouille la ligne de la variante le temps du calcul (deux ventes ou
 *   deux saisies simultanées ne se marchent pas dessus) ;
 * - refuse un stock négatif ;
 * - écrit un mouvement, et une ligne au journal pour les saisies du personnel.
 */
final class Stock
{
    public static function ajuster(Variante $variante, int $quantite, string $motif, ?string $note = null): MouvementStock
    {
        if ($quantite === 0) {
            throw new InvalidArgumentException('Un mouvement de stock porte sur au moins une unité.');
        }

        if (! array_key_exists($motif, MouvementStock::motifs()) || $motif === MouvementStock::STOCK_INITIAL) {
            throw new InvalidArgumentException("Motif de mouvement de stock inconnu : {$motif}");
        }

        $variante->loadMissing('produit');

        return DB::transaction(function () use ($variante, $quantite, $motif, $note): MouvementStock {
            $actuel = (int) Variante::query()->whereKey($variante->getKey())->lockForUpdate()->value('stock');
            $nouveau = $actuel + $quantite;

            if ($nouveau < 0) {
                throw new StockInsuffisant($variante, $actuel, -$quantite);
            }

            Variante::query()->whereKey($variante->getKey())->update(['stock' => $nouveau, 'updated_at' => now()]);
            $variante->forceFill(['stock' => $nouveau])->syncOriginalAttribute('stock');

            $mouvement = MouvementStock::create([
                'variante_id' => $variante->getKey(),
                'quantite' => $quantite,
                'stock_apres' => $nouveau,
                'motif' => $motif,
                'note' => $note,
                'utilisateur_id' => Auth::guard('personnel')->id(),
            ]);

            JournalActivite::consigner(
                'catalogue.stock',
                sprintf('Stock de « %s » : %+d (%s), reste %d', $variante->libelle(), $quantite, MouvementStock::motifs()[$motif], $nouveau),
                $variante,
                ['avant' => $actuel, 'apres' => $nouveau, 'motif' => $motif, 'note' => $note],
            );

            return $mouvement;
        });
    }
}
