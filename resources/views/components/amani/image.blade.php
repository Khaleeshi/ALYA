{{--
    Photo responsive : AVIF puis WebP, cinq largeurs, place réservée par les
    dimensions de l'original. « prioritaire » pour l'image principale d'un
    écran (chargée tout de suite) ; toutes les autres sont différées.
--}}
@props([
    'media',
    'alt' => '',
    'sizes' => '100vw',
    'prioritaire' => false,
])

@php
    $sources = \App\Support\Medias\DeclinaisonsImages::sources($media);
@endphp

<picture>
    @if ($sources['avif'] !== '')
        <source type="image/avif" srcset="{{ $sources['avif'] }}" sizes="{{ $sizes }}">
    @endif
    @if ($sources['webp'] !== '')
        <source type="image/webp" srcset="{{ $sources['webp'] }}" sizes="{{ $sizes }}">
    @endif
    <img
        src="{{ $sources['repli'] }}"
        alt="{{ $alt }}"
        @if ($sources['largeur'] && $sources['hauteur']) width="{{ $sources['largeur'] }}" height="{{ $sources['hauteur'] }}" @endif
        loading="{{ $prioritaire ? 'eager' : 'lazy' }}"
        decoding="async"
        @if ($prioritaire) fetchpriority="high" @endif
        {{ $attributes }}
    >
</picture>
