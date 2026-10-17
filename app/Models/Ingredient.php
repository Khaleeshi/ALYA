<?php

namespace App\Models;

use App\Models\Concerns\AvecPhotos;
use App\Models\Concerns\AvecSlug;
use Database\Factories\IngredientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

/**
 * Ingrédient clé (maquette 2) : photo ronde, nom, bienfait en une phrase.
 * Sert aussi de filtre dans la boutique (cahier §5.2).
 *
 * @property string $nom
 * @property string $slug
 * @property ?string $bienfait
 */
class Ingredient extends Model implements HasMedia
{
    use AvecPhotos;
    use AvecSlug;

    /** @use HasFactory<IngredientFactory> */
    use HasFactory;

    use HasTranslations;

    public const PHOTO = 'photo';

    protected $table = 'ingredients';

    /** @var list<string> */
    public array $translatable = ['nom', 'bienfait'];

    protected $fillable = [
        'nom',
        'slug',
        'bienfait',
    ];

    protected static function booted(): void
    {
        static::updated(function (self $ingredient): void {
            if ($ingredient->wasChanged('nom')) {
                $ingredient->produits()->get()->each->actualiserTexteRecherche();
            }
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::PHOTO)
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif']);
    }

    /**
     * @return BelongsToMany<Produit, $this>
     */
    public function produits(): BelongsToMany
    {
        return $this->belongsToMany(Produit::class);
    }

    public function photo(): ?Media
    {
        return $this->getFirstMedia(self::PHOTO);
    }
}
