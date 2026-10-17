<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Adresse lisible (slug) d'un élément du catalogue : « gloss-nude-glow ».
 * Laissée vide, elle se déduit du nom français ; elle reste unique en
 * ajoutant -2, -3… si besoin. Commune à toutes les langues.
 */
trait AvecSlug
{
    protected static function bootAvecSlug(): void
    {
        static::saving(function (self $modele): void {
            $base = Str::slug((string) ($modele->slug ?: $modele->getTranslation('nom', 'fr', false)));
            $base = $base === '' ? Str::lower(class_basename($modele)) : Str::limit($base, 100, '');

            $slug = $base;
            $suite = 2;

            while (static::query()->where('slug', $slug)->whereKeyNot($modele->getKey())->exists()) {
                $slug = $base.'-'.$suite++;
            }

            $modele->slug = $slug;
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
