<?php

namespace App\Models;

use App\Models\Concerns\AvecPhotos;
use App\Support\Montant;
use Database\Factories\VarianteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use LogicException;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

/**
 * Ce que la cliente met au panier : une couleur ou un format d'un produit,
 * avec sa référence, son prix éventuel et son stock.
 *
 * Le stock est fixé à la création (stock de départ, noté dans les
 * mouvements), puis ne change que par App\Support\Catalogue\Stock : toute
 * autre modification est refusée, pour que chaque unité ait une trace.
 *
 * @property int $produit_id
 * @property ?string $nom
 * @property string $reference
 * @property ?string $couleur
 * @property ?int $prix
 * @property int $stock
 * @property int $position
 * @property bool $active
 */
class Variante extends Model implements HasMedia
{
    use AvecPhotos;

    /** @use HasFactory<VarianteFactory> */
    use HasFactory;

    use HasTranslations;

    public const EN_STOCK = 'en_stock';

    public const STOCK_LIMITE = 'stock_limite';

    public const RUPTURE = 'rupture';

    public const PHOTO = 'photo';

    protected $table = 'variantes';

    /** @var list<string> */
    public array $translatable = ['nom'];

    protected $fillable = [
        'produit_id',
        'nom',
        'reference',
        'couleur',
        'prix',
        'stock',
        'position',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'prix' => 'integer',
            'stock' => 'integer',
            'position' => 'integer',
            'active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $variante): void {
            $variante->reference = filled($variante->reference)
                ? mb_strtoupper(trim($variante->reference))
                : self::referenceLibre($variante->produit_id);
        });

        static::created(function (self $variante): void {
            if ($variante->stock > 0) {
                MouvementStock::create([
                    'variante_id' => $variante->getKey(),
                    'quantite' => $variante->stock,
                    'stock_apres' => $variante->stock,
                    'motif' => MouvementStock::STOCK_INITIAL,
                    'utilisateur_id' => Auth::guard('personnel')->id(),
                ]);
            }
        });

        static::updating(function (self $variante): void {
            if ($variante->isDirty('stock')) {
                throw new LogicException('Le stock ne change que par un mouvement de stock (App\Support\Catalogue\Stock).');
            }
        });

        static::updated(function (self $variante): void {
            if ($variante->wasChanged('prix')) {
                $avant = $variante->getOriginal('prix');

                JournalActivite::consigner(
                    'catalogue.prix',
                    sprintf('Prix de « %s » : %s → %s', $variante->libelle(), $avant === null ? 'prix du produit' : Montant::fcfa((int) $avant), $variante->prix === null ? 'prix du produit' : Montant::fcfa($variante->prix)),
                    $variante,
                    ['avant' => $avant, 'apres' => $variante->prix],
                );
            }
        });

        // Les couleurs et références entrent dans la recherche du produit.
        static::saved(fn (self $variante) => Produit::find($variante->produit_id)?->actualiserTexteRecherche());
        static::deleted(fn (self $variante) => Produit::find($variante->produit_id)?->actualiserTexteRecherche());
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::PHOTO)
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif']);
    }

    /**
     * @return BelongsTo<Produit, $this>
     */
    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class);
    }

    /**
     * @return HasMany<MouvementStock, $this>
     */
    public function mouvements(): HasMany
    {
        return $this->hasMany(MouvementStock::class)->latest('cree_le')->latest('id');
    }

    public function prixFinal(): int
    {
        return $this->prix ?? $this->produit->prix;
    }

    /**
     * En stock, stock limité (au seuil d'alerte du produit ou en dessous) ou rupture.
     */
    public function etatStock(): string
    {
        return match (true) {
            $this->stock <= 0 => self::RUPTURE,
            $this->stock <= $this->produit->seuil_alerte => self::STOCK_LIMITE,
            default => self::EN_STOCK,
        };
    }

    /**
     * « Gloss Amani – Nude Glow », ou le seul nom du produit sans variante nommée.
     */
    public function libelle(): string
    {
        $produit = $this->produit->nom;

        return filled($this->nom) ? $produit.' – '.$this->nom : $produit;
    }

    public function photo(): ?Media
    {
        return $this->getFirstMedia(self::PHOTO);
    }

    /**
     * AMN-00012-03 : numéro du produit, puis rang de la variante.
     */
    private static function referenceLibre(int $produitId): string
    {
        $rang = self::query()->where('produit_id', $produitId)->count() + 1;

        do {
            $reference = sprintf('AMN-%05d-%02d', $produitId, $rang++);
        } while (self::query()->where('reference', $reference)->exists());

        return $reference;
    }
}
