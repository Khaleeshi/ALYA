<?php

namespace App\Models;

use App\Models\Concerns\AvecPhotos;
use App\Models\Concerns\AvecSlug;
use Database\Factories\CategorieFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Translatable\HasTranslations;

/**
 * Catégorie de la boutique (cahier §1.1) : Gloss, Skincare, Maquillage…
 *
 * @property string $nom
 * @property string $slug
 * @property ?string $description
 * @property int $position
 * @property bool $visible
 */
class Categorie extends Model implements HasMedia
{
    use AvecPhotos;
    use AvecSlug;

    /** @use HasFactory<CategorieFactory> */
    use HasFactory;

    use HasTranslations;

    public const COUVERTURE = 'couverture';

    protected $table = 'categories';

    /** @var list<string> */
    public array $translatable = ['nom', 'description'];

    protected $fillable = [
        'nom',
        'slug',
        'description',
        'position',
        'visible',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'visible' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Le nom de la catégorie entre dans la recherche de ses produits.
        static::updated(function (self $categorie): void {
            if ($categorie->wasChanged('nom')) {
                $categorie->produits()->get()->each->actualiserTexteRecherche();
            }
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::COUVERTURE)->singleFile();
    }

    /**
     * @return HasMany<Produit, $this>
     */
    public function produits(): HasMany
    {
        return $this->hasMany(Produit::class);
    }

    /**
     * @param  Builder<self>  $requete
     */
    public function scopeVisibles(Builder $requete): void
    {
        $requete->where('visible', true)->orderBy('position')->orderBy('id');
    }
}
