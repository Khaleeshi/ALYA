<?php

namespace App\Models;

use App\Models\Concerns\AvecSlug;
use Database\Factories\EffetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

/**
 * Effet ou besoin (hydratation, éclat, tenue…) : filtre de la boutique
 * (cahier §5.2). Un filtre ne propose que les effets des produits affichés.
 *
 * @property string $nom
 * @property string $slug
 * @property int $position
 */
class Effet extends Model
{
    use AvecSlug;

    /** @use HasFactory<EffetFactory> */
    use HasFactory;

    use HasTranslations;

    protected $table = 'effets';

    /** @var list<string> */
    public array $translatable = ['nom'];

    protected $fillable = [
        'nom',
        'slug',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::updated(function (self $effet): void {
            if ($effet->wasChanged('nom')) {
                $effet->produits()->get()->each->actualiserTexteRecherche();
            }
        });
    }

    /**
     * @return BelongsToMany<Produit, $this>
     */
    public function produits(): BelongsToMany
    {
        return $this->belongsToMany(Produit::class);
    }
}
