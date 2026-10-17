<?php

namespace Tests\Fixtures;

use App\Models\Concerns\AvecPhotos;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

/**
 * Modèle de test qui porte des photos, en attendant les produits (phase 2).
 */
class ModeleAvecPhotos extends Model implements HasMedia
{
    use AvecPhotos;

    protected $table = 'modeles_avec_photos';

    protected $guarded = [];
}
