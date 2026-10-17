<?php

namespace App\Models\Concerns;

use App\Support\Medias\DeclinaisonsImages;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * À utiliser sur tout modèle qui porte des photos (produits, teintes,
 * collections, bannières…) : les déclinaisons AVIF et WebP sont produites
 * automatiquement. Le modèle doit implémenter Spatie\MediaLibrary\HasMedia.
 */
trait AvecPhotos
{
    use InteractsWithMedia;

    public function registerMediaConversions(?Media $media = null): void
    {
        DeclinaisonsImages::declarer($this, $media);
    }
}
