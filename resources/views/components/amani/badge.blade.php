{{--
    Badge de vignette (maquettes) : « Best-seller » (cacao), « Promo » (rouge),
    « Nouveau » ou « Édition limitée » (bronze), « clair » pour un fond sombre.
--}}
@props(['ton' => 'cacao'])

@php
    $styles = [
        'cacao' => 'bg-cacao text-blanc',
        'rouge' => 'bg-rouge text-blanc',
        'bronze' => 'bg-bronze-fonce text-blanc',
        'clair' => 'bg-blanc text-brun',
    ][$ton] ?? throw new InvalidArgumentException("Ton de badge inconnu : {$ton}");
@endphp

<span {{ $attributes->class("etiquette inline-flex items-center rounded-badge px-2.5 py-1 {$styles}") }}>{{ $slot }}</span>
