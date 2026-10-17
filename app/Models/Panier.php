<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Panier d'une visiteuse (retrouvé par le jeton de son cookie) ou d'une
 * cliente (retrouvé par son compte). Se manipule par
 * App\Support\Panier\GestionPanier.
 *
 * Un panier de visiteuse oublié depuis 30 jours, ou de cliente depuis
 * 180 jours, est effacé chaque nuit (model:prune).
 *
 * @property ?string $jeton
 * @property ?int $cliente_id
 */
class Panier extends Model
{
    use MassPrunable;

    protected $table = 'paniers';

    protected $fillable = [
        'jeton',
        'cliente_id',
    ];

    /**
     * @return BelongsTo<Cliente, $this>
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * @return HasMany<LignePanier, $this>
     */
    public function lignes(): HasMany
    {
        return $this->hasMany(LignePanier::class)->orderBy('id')->chaperone();
    }

    /**
     * Lignes prêtes à afficher : variante, produit, photos.
     *
     * @return Collection<int, LignePanier>
     */
    public function lignesCompletes(): Collection
    {
        return $this->lignes()
            ->with(['variante.media', 'variante.produit.media', 'variante.produit.categorie'])
            ->get();
    }

    /**
     * @return Builder<self>
     */
    public function prunable(): Builder
    {
        return static::query()
            ->where(fn (Builder $q) => $q->whereNull('cliente_id')->where('updated_at', '<', now()->subDays(30)))
            ->orWhere(fn (Builder $q) => $q->whereNotNull('cliente_id')->where('updated_at', '<', now()->subDays(180)));
    }
}
