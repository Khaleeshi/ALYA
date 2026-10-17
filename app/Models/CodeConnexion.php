<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Code à usage unique envoyé par SMS. Seule son empreinte est conservée.
 *
 * @property string $telephone
 * @property string $empreinte
 * @property int $tentatives
 * @property Carbon $expire_le
 * @property ?Carbon $utilise_le
 */
class CodeConnexion extends Model
{
    use MassPrunable;

    public const UPDATED_AT = null;

    public const CREATED_AT = 'cree_le';

    protected $table = 'codes_connexion';

    protected $fillable = [
        'telephone',
        'empreinte',
        'expire_le',
        'adresse_ip',
    ];

    protected function casts(): array
    {
        return [
            'tentatives' => 'integer',
            'expire_le' => 'datetime',
            'utilise_le' => 'datetime',
            'cree_le' => 'datetime',
        ];
    }

    /**
     * @return Builder<self>
     */
    public function prunable(): Builder
    {
        return static::where('cree_le', '<', now()->subDay());
    }
}
