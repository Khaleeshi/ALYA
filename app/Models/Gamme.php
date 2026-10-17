<?php

namespace App\Models;

use App\Models\Concerns\AvecPhotos;
use App\Models\Concerns\AvecSlug;
use Database\Factories\GammeFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Translatable\HasTranslations;

/**
 * Collection de produits (cahier §3 et §12.2, maquette 3) : permanente,
 * saisonnière ou édition limitée, avec un ordre et des dates d'affichage.
 * Elle s'appelle « Gamme » dans le code parce que Laravel utilise déjà le
 * mot Collection ; les clientes et l'administration lisent « collection ».
 *
 * @property string $nom
 * @property string $slug
 * @property string $type
 * @property ?string $accroche
 * @property ?string $description
 * @property int $position
 * @property bool $en_ligne
 * @property ?Carbon $debut_le
 * @property ?Carbon $fin_le
 */
class Gamme extends Model implements HasMedia
{
    use AvecPhotos;
    use AvecSlug;

    /** @use HasFactory<GammeFactory> */
    use HasFactory;

    use HasTranslations;

    public const PERMANENTE = 'permanente';

    public const SAISONNIERE = 'saisonniere';

    public const EDITION_LIMITEE = 'edition_limitee';

    public const COUVERTURE = 'couverture';

    protected $table = 'gammes';

    /** @var list<string> */
    public array $translatable = ['nom', 'accroche', 'description'];

    protected $fillable = [
        'nom',
        'slug',
        'type',
        'accroche',
        'description',
        'position',
        'en_ligne',
        'debut_le',
        'fin_le',
    ];

    /**
     * @return array<string, string>
     */
    public static function types(): array
    {
        return [
            self::PERMANENTE => 'Collection permanente',
            self::SAISONNIERE => 'Collection saisonnière',
            self::EDITION_LIMITEE => 'Édition limitée',
        ];
    }

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'en_ligne' => 'boolean',
            'debut_le' => 'datetime',
            'fin_le' => 'datetime',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::COUVERTURE)->singleFile();
    }

    /**
     * @return BelongsToMany<Produit, $this>
     */
    public function produits(): BelongsToMany
    {
        return $this->belongsToMany(Produit::class, 'gamme_produit')
            ->withPivot('position')
            ->orderByPivot('position');
    }

    /**
     * Collections montrées aux clientes : visibles et dans leurs dates.
     *
     * @param  Builder<self>  $requete
     */
    public function scopeVisibles(Builder $requete): void
    {
        $maintenant = now();

        $requete->where('en_ligne', true)
            ->where(fn (Builder $q) => $q->whereNull('debut_le')->orWhere('debut_le', '<=', $maintenant))
            ->where(fn (Builder $q) => $q->whereNull('fin_le')->orWhere('fin_le', '>=', $maintenant))
            ->orderBy('position')
            ->orderBy('id');
    }

    public function estVisible(): bool
    {
        return $this->en_ligne
            && ($this->debut_le === null || $this->debut_le->isPast())
            && ($this->fin_le === null || $this->fin_le->isFuture());
    }
}
