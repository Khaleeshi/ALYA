<?php

namespace App\Support\Medias;

use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Chaîne d'images ALYA (audit 7.2) : chaque photo déposée est déclinée en
 * cinq largeurs, en AVIF et en WebP, dans la file « medias ».
 *
 * Les largeurs vont de 400 px (vignette sur téléphone) à 2400 px (le minimum
 * demandé au photographe, brief 9.5) : une photo n'est jamais agrandie, et
 * les largeurs plus grandes que l'original ne sont pas produites.
 */
final class DeclinaisonsImages
{
    /** @var list<int> */
    public const LARGEURS = [400, 800, 1200, 1600, 2400];

    /** Format => qualité de compression. */
    public const FORMATS = [
        'avif' => 55,
        'webp' => 78,
    ];

    /** Format servi dans la balise <img> aux navigateurs qui ne lisent pas <source>. */
    public const FORMAT_REPLI = 'webp';

    public static function nom(string $format, int $largeur): string
    {
        return $format.'-'.$largeur;
    }

    /**
     * Déclare les conversions sur un modèle (à appeler depuis registerMediaConversions).
     */
    public static function declarer(HasMedia $modele, ?Media $media = null): void
    {
        foreach (self::largeursPour($media) as $largeur) {
            foreach (self::FORMATS as $format => $qualite) {
                $modele->addMediaConversion(self::nom($format, $largeur))
                    ->fit(Fit::Max, $largeur, $largeur * 4)
                    ->format($format)
                    ->quality($qualite)
                    ->nonOptimized();
            }
        }
    }

    /**
     * Largeurs utiles pour une photo : jamais plus large que l'original,
     * et toujours au moins la plus petite.
     *
     * @return list<int>
     */
    public static function largeursPour(?Media $media): array
    {
        $largeurOriginale = $media?->getCustomProperty('largeur');

        if (! is_int($largeurOriginale)) {
            return self::LARGEURS;
        }

        $largeurs = array_values(array_filter(self::LARGEURS, fn (int $largeur): bool => $largeur <= $largeurOriginale));

        return $largeurs === [] ? [self::LARGEURS[0]] : $largeurs;
    }

    /**
     * Retient la taille de l'original dès son dépôt : elle sert à ne produire
     * que les largeurs utiles et à réserver la place de l'image dans la page
     * (pas de saut de mise en page, audit section 10).
     */
    public static function lireDimensions(MediaHasBeenAddedEvent $evenement): void
    {
        $media = $evenement->media;

        if (! str_starts_with((string) $media->mime_type, 'image/')) {
            return;
        }

        $taille = @getimagesize($media->getPath());

        if ($taille === false) {
            return;
        }

        $media->setCustomProperty('largeur', $taille[0]);
        $media->setCustomProperty('hauteur', $taille[1]);
        $media->saveQuietly();
    }

    /**
     * Sources prêtes pour une balise <picture> : un srcset par format, à partir
     * des seules déclinaisons déjà produites.
     *
     * @return array{avif: string, webp: string, repli: string, largeur: ?int, hauteur: ?int}
     */
    public static function sources(Media $media): array
    {
        $largeurOriginale = $media->getCustomProperty('largeur');
        $sources = ['largeur' => $largeurOriginale, 'hauteur' => $media->getCustomProperty('hauteur')];
        $repli = null;

        foreach (array_keys(self::FORMATS) as $format) {
            $morceaux = [];

            foreach (self::LARGEURS as $largeur) {
                $nom = self::nom($format, $largeur);

                if (! $media->hasGeneratedConversion($nom)) {
                    continue;
                }

                $largeurReelle = is_int($largeurOriginale) ? min($largeur, $largeurOriginale) : $largeur;
                $morceaux[] = $media->getUrl($nom).' '.$largeurReelle.'w';

                if ($format === self::FORMAT_REPLI && $largeur <= 1200) {
                    $repli = $media->getUrl($nom);
                }
            }

            $sources[$format] = implode(', ', $morceaux);
        }

        // Tant qu'aucune déclinaison n'est prête (file en cours), l'original sert.
        $sources['repli'] = $repli ?? $media->getUrl();

        return $sources;
    }
}
