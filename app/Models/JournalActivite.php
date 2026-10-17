<?php

namespace App\Models;

use App\Exceptions\JournalImmuable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Ligne du journal d'activité (cahier §12.3) : qui a fait quoi, sur quoi, quand.
 * Ajout seul : toute modification ou suppression lève JournalImmuable, et la
 * base de données refuse aussi les UPDATE et DELETE directs.
 *
 * @property string $auteur_type
 * @property ?int $auteur_id
 * @property string $auteur_libelle
 * @property string $action
 * @property string $description
 * @property ?array $donnees
 * @property Carbon $cree_le
 */
class JournalActivite extends Model
{
    public const AUTEUR_SYSTEME = 'systeme';

    public const AUTEUR_PERSONNEL = 'personnel';

    public const AUTEUR_CLIENTE = 'cliente';

    public const UPDATED_AT = null;

    public const CREATED_AT = 'cree_le';

    protected $table = 'journal_activites';

    protected $fillable = [
        'auteur_type',
        'auteur_id',
        'auteur_libelle',
        'action',
        'description',
        'donnees',
        'adresse_ip',
    ];

    protected function casts(): array
    {
        return [
            'donnees' => 'array',
            'cree_le' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::updating(fn () => throw new JournalImmuable);
        static::deleting(fn () => throw new JournalImmuable);
    }

    /**
     * Écrit une ligne. L'auteur est la personne connectée (personnel d'abord,
     * puis cliente) ; sans personne connectée, c'est le Système.
     */
    public static function consigner(
        string $action,
        string $description,
        ?Model $sujet = null,
        array $donnees = [],
        Utilisateur|Cliente|null $auteur = null,
    ): self {
        $auteur ??= Auth::guard('personnel')->user() ?? Auth::guard('web')->user();

        [$type, $libelle] = match (true) {
            $auteur instanceof Utilisateur => [self::AUTEUR_PERSONNEL, $auteur->libelleJournal()],
            $auteur instanceof Cliente => [self::AUTEUR_CLIENTE, 'Cliente · '.$auteur->prenom],
            default => [self::AUTEUR_SYSTEME, 'Système'],
        };

        $ligne = new self([
            'auteur_type' => $type,
            'auteur_id' => $auteur?->getKey(),
            'auteur_libelle' => $libelle,
            'action' => $action,
            'description' => mb_substr($description, 0, 500),
            'donnees' => $donnees === [] ? null : $donnees,
            'adresse_ip' => app()->runningInConsole() ? null : request()->ip(),
        ]);

        $ligne->sujet()->associate($sujet);
        $ligne->save();

        return $ligne;
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function sujet(): MorphTo
    {
        return $this->morphTo();
    }
}
