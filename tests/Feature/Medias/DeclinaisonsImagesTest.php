<?php

use App\Support\Medias\DeclinaisonsImages;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\Fixtures\ModeleAvecPhotos;

beforeEach(function () {
    Storage::fake('medias');

    Schema::create('modeles_avec_photos', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
    });
});

/**
 * Photo JPEG de test aux dimensions demandées (dégradé noir vers or).
 */
function photoDeTest(int $largeur, int $hauteur): string
{
    $chemin = tempnam(sys_get_temp_dir(), 'amani').'.jpg';

    $image = new Imagick;
    $image->newPseudoImage($largeur, $hauteur, 'gradient:#070707-#c8a95a');
    $image->setImageFormat('jpeg');
    $image->writeImage($chemin);
    $image->clear();

    return $chemin;
}

it('décline une photo 4K en cinq largeurs, en AVIF et en WebP', function () {
    $media = ModeleAvecPhotos::create()
        ->addMedia(photoDeTest(3840, 2160))
        ->toMediaCollection()
        ->refresh();

    expect($media->getCustomProperty('largeur'))->toBe(3840)
        ->and($media->getCustomProperty('hauteur'))->toBe(2160);

    foreach (DeclinaisonsImages::FORMATS as $format => $qualite) {
        foreach (DeclinaisonsImages::LARGEURS as $largeur) {
            $nom = DeclinaisonsImages::nom($format, $largeur);
            $chemin = $media->getPath($nom);

            expect($media->hasGeneratedConversion($nom))->toBeTrue("{$nom} n'a pas été produite")
                ->and(file_exists($chemin))->toBeTrue();

            $declinaison = new Imagick($chemin);

            expect(strtolower($declinaison->getImageFormat()))->toBe($format)
                ->and($declinaison->getImageWidth())->toBe($largeur)
                ->and($declinaison->getImageHeight())->toBe((int) round($largeur * 2160 / 3840));
        }
    }
});

it("n'agrandit jamais une photo et ne produit que les largeurs utiles", function () {
    $media = ModeleAvecPhotos::create()
        ->addMedia(photoDeTest(1000, 1250))
        ->toMediaCollection()
        ->refresh();

    expect(collect($media->generated_conversions)->filter()->keys()->sort()->values()->all())
        ->toBe(['avif-400', 'avif-800', 'webp-400', 'webp-800']);
});

it('prépare une balise picture avec AVIF, WebP et les dimensions de l\'original', function () {
    $media = ModeleAvecPhotos::create()
        ->addMedia(photoDeTest(1600, 2000))
        ->toMediaCollection()
        ->refresh();

    $html = Blade::render('<x-amani.image :media="$media" alt="Gloss" sizes="50vw" />', ['media' => $media]);

    expect($html)
        ->toContain('type="image/avif"')
        ->toContain('type="image/webp"')
        ->toContain('avif-1600')
        ->toContain('1600w')
        ->toContain('width="1600"')
        ->toContain('height="2000"')
        ->toContain('loading="lazy"')
        ->not->toContain('avif-2400');
});
