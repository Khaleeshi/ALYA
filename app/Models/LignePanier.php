<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Une variante et sa quantité dans un panier. Le prix est toujours celui
 * du moment ; rien n'est réservé (cahier §6.1).
 *
 * @property int $panier_id
 * @property int $variante_id
 * @property int $quantite
 */
class LignePanier extends Model
{
    protected $table = 'lignes_panier';

    protected $fillable = [
        'panier_id',
        'variante_id',
        'quantite',
    ];

    protected function casts(): array
    {
        return [
            'quantite' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Panier, $this>
     */
    public function panier(): BelongsTo
    {
        return $this->belongsTo(Panier::class);
    }

    /**
     * @return BelongsTo<Variante, $this>
     */
    public function variante(): BelongsTo
    {
        return $this->belongsTo(Variante::class);
    }

    /**
     * Encore en vente : variante active, produit visible, stock non nul.
     */
    public function estDisponible(): bool
    {
        $variante = $this->variante;

        return $variante->active && $variante->stock > 0 && $variante->produit->estVisible();
    }

    public function prixUnitaire(): int
    {
        return $this->variante->prixFinal();
    }

    public function total(): int
    {
        return $this->prixUnitaire() * $this->quantite;
    }
}
