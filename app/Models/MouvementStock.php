<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use LogicException;

/**
 * Ligne du journal des mouvements de stock (cahier §12.2) : quantité ajoutée
 * ou retirée, stock restant, motif, auteur. Ajout seul.
 *
 * Les motifs liés aux commandes (vente, annulation, retour) arriveront avec
 * les commandes : le stock ne baisse qu'après un paiement confirmé (§6.3).
 *
 * @property int $variante_id
 * @property int $quantite
 * @property int $stock_apres
 * @property string $motif
 * @property ?string $note
 * @property ?int $utilisateur_id
 * @property Carbon $cree_le
 */
class MouvementStock extends Model
{
    public const STOCK_INITIAL = 'stock_initial';

    public const REAPPROVISIONNEMENT = 'reapprovisionnement';

    public const CORRECTION = 'correction';

    public const UPDATED_AT = null;

    public const CREATED_AT = 'cree_le';

    protected $table = 'mouvements_stock';

    protected $fillable = [
        'variante_id',
        'quantite',
        'stock_apres',
        'motif',
        'note',
        'utilisateur_id',
    ];

    /**
     * @return array<string, string>
     */
    public static function motifs(): array
    {
        return [
            self::STOCK_INITIAL => 'Stock de départ',
            self::REAPPROVISIONNEMENT => 'Réapprovisionnement',
            self::CORRECTION => 'Correction après inventaire',
        ];
    }

    protected function casts(): array
    {
        return [
            'quantite' => 'integer',
            'stock_apres' => 'integer',
            'cree_le' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Un mouvement de stock ne se modifie pas.'));
        static::deleting(fn () => throw new LogicException('Un mouvement de stock ne se supprime pas.'));
    }

    /**
     * @return BelongsTo<Variante, $this>
     */
    public function variante(): BelongsTo
    {
        return $this->belongsTo(Variante::class);
    }

    /**
     * @return BelongsTo<Utilisateur, $this>
     */
    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class);
    }
}
