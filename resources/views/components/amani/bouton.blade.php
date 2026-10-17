{{--
    Boutons AMANI (maquettes) : en pilule.
    « principal » : dégradé bronze vers or, texte crème (« Ajouter au panier »).
    « secondaire » : fond nude pâle, texte brun (« Acheter maintenant »).
    « contour » : filet bronze (actions de second rang).
    « lien » : texte souligné d'or, avec flèche si demandé (« Voir tout → »).
    icone / iconeApres : nom d'une icône <x-amani.icone> avant ou après le texte.
--}}
@props([
    'variante' => 'principal',
    'taille' => 'normale',
    'href' => null,
    'type' => 'submit',
    'pleineLargeur' => false,
    'icone' => null,
    'iconeApres' => null,
])

@php
    $styles = [
        'principal' => 'fond-dore text-blanc shadow-[0_6px_16px_rgb(133_85_44_/_0.25)] hover:brightness-110',
        'secondaire' => 'bg-nude-pale text-brun hover:bg-nude',
        'contour' => 'border border-bronze text-bronze-fonce hover:bg-nude-pale',
        'lien' => 'text-brun underline decoration-or underline-offset-4 hover:text-bronze-fonce',
    ];

    $tailles = [
        'normale' => 'h-12 px-7 text-[15px]',
        'petite' => 'h-9 px-5 text-sm',
    ];

    $estLien = $variante === 'lien';

    $classes = [
        'inline-flex items-center justify-center gap-2.5 font-medium transition duration-200 ease-amani',
        'disabled:cursor-not-allowed disabled:opacity-50 aria-disabled:pointer-events-none aria-disabled:opacity-50',
        $styles[$variante] ?? throw new InvalidArgumentException("Variante de bouton inconnue : {$variante}"),
        $estLien ? 'text-[15px]' : 'rounded-full '.($tailles[$taille] ?? throw new InvalidArgumentException("Taille de bouton inconnue : {$taille}")),
        'w-full' => $pleineLargeur,
    ];

    $tailleIcone = $taille === 'petite' ? 16 : 20;
@endphp

@if ($href !== null)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        @if ($icone)<x-amani.icone :nom="$icone" :taille="$tailleIcone" />@endif
        <span>{{ $slot }}</span>
        @if ($iconeApres)<x-amani.icone :nom="$iconeApres" :taille="$tailleIcone" />@endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>
        @if ($icone)<x-amani.icone :nom="$icone" :taille="$tailleIcone" />@endif
        <span>{{ $slot }}</span>
        @if ($iconeApres)<x-amani.icone :nom="$iconeApres" :taille="$tailleIcone" />@endif
    </button>
@endif
