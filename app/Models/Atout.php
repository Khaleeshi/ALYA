<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

/**
 * Atout illustré d'une fiche produit (maquette 2) : une icône et une phrase
 * courte, quatre au plus sous les boutons d'achat.
 *
 * @property int $produit_id
 * @property string $icone
 * @property string $texte
 * @property int $position
 */
class Atout extends Model
{
    use HasTranslations;

    public const MAXIMUM = 4;

    protected $table = 'atouts';

    /** @var list<string> */
    public array $translatable = ['texte'];

    protected $fillable = [
        'produit_id',
        'icone',
        'texte',
        'position',
    ];

    /**
     * Icônes proposées dans l'administration (composant <x-amani.icone>).
     *
     * @return array<string, string>
     */
    public static function icones(): array
    {
        return [
            'levres' => 'Lèvres',
            'eclat' => 'Éclat',
            'goutte' => 'Goutte (hydratation)',
            'feuille' => 'Feuille (naturel)',
            'bouclier' => 'Bouclier (protection, tolérance)',
            'coeur' => 'Cœur',
            'diamant' => 'Diamant',
            'etoile' => 'Étoile',
            'horloge' => 'Horloge (tenue)',
            'personnes' => 'Personnes (tous les types)',
        ];
    }

    protected function casts(): array
    {
        return [
            'position' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Produit, $this>
     */
    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class);
    }
}
