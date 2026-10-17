<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Accord donné ou retiré par une cliente (cahier §4.2 et §15). Les messages liés à une
 * commande partent toujours ; seules les offres et nouveautés demandent un accord.
 *
 * @property string $type
 * @property bool $accorde
 * @property string $origine
 */
class Consentement extends Model
{
    public const OFFRES = 'offres';

    public const ORIGINE_INSCRIPTION = 'inscription';

    public const UPDATED_AT = null;

    public const CREATED_AT = 'cree_le';

    protected $table = 'consentements';

    protected $fillable = [
        'type',
        'accorde',
        'origine',
        'adresse_ip',
    ];

    protected function casts(): array
    {
        return [
            'accorde' => 'boolean',
            'cree_le' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Cliente, $this>
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}
