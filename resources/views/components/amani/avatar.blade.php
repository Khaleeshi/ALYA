{{--
    Avatar à initiales (maquettes 6 et 8 : « PB »). Aucune photo de visage
    n'est demandée aux clientes.
--}}
@props(['nom', 'taille' => 'normale'])

@php
    $initiales = collect(preg_split('/\s+/', trim($nom)))
        ->filter()
        ->take(2)
        ->map(fn (string $morceau): string => mb_strtoupper(mb_substr($morceau, 0, 1)))
        ->implode('');

    $classes = match ($taille) {
        'petite' => 'size-9 text-sm',
        'grande' => 'size-16 text-xl',
        default => 'size-12 text-base',
    };
@endphp

<span {{ $attributes->class("inline-flex shrink-0 items-center justify-center rounded-full bg-nude-pale font-semibold text-bronze-fonce {$classes}") }} aria-hidden="true">{{ $initiales }}</span>
