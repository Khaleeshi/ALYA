{{--
    Icônes fonctionnelles uniquement (audit 9.3) : trait fin de 1,25 px, 20 px.
    Aucune icône illustrative. Les logos de marques (Wave, Orange Money,
    WhatsApp, réseaux) ne sont jamais redessinés ici.
--}}
@props(['nom', 'taille' => 20])

@php
    $traces = [
        'menu' => '<path d="M3 6h14M3 10h14M3 14h14"/>',
        'recherche' => '<circle cx="8.75" cy="8.75" r="5.25"/><path d="m12.5 12.5 4.5 4.5"/>',
        'compte' => '<circle cx="10" cy="6.75" r="3.25"/><path d="M3.75 17c.75-3.25 3.25-5 6.25-5s5.5 1.75 6.25 5"/>',
        'sac' => '<path d="M4 7h12l-.9 10H4.9L4 7Z"/><path d="M7.5 7V5.5a2.5 2.5 0 0 1 5 0V7"/>',
        'coeur' => '<path d="M10 16.25S3.5 12.5 3.5 7.9A3.15 3.15 0 0 1 10 6.4a3.15 3.15 0 0 1 6.5 1.5c0 4.6-6.5 8.35-6.5 8.35Z"/>',
        'fermer' => '<path d="m5 5 10 10M15 5 5 15"/>',
        'chevron' => '<path d="m8 4.5 5.5 5.5L8 15.5"/>',
        'plus' => '<path d="M10 4v12M4 10h12"/>',
        'moins' => '<path d="M4 10h12"/>',
        'coche' => '<path d="m4.5 10.5 3.5 3.5 7.5-8"/>',
        'alerte' => '<path d="M10 3.25 17.75 16.75H2.25L10 3.25Z"/><path d="M10 8.25v4M10 14.25v.5"/>',
    ];

    $trace = $traces[$nom] ?? throw new InvalidArgumentException("Icône inconnue : {$nom}");
@endphp

<svg
    {{ $attributes->class('shrink-0') }}
    width="{{ $taille }}"
    height="{{ $taille }}"
    viewBox="0 0 20 20"
    fill="none"
    stroke="currentColor"
    stroke-width="1.25"
    stroke-linecap="square"
    aria-hidden="true"
    focusable="false"
>{!! $trace !!}</svg>
