{{--
    Boutons ALYA (audit 9.3).
    « principal » : or plein. UN SEUL par écran, pour l'action principale.
    « secondaire » : filet doré. « discret » : filet sombre. « lien » : texte souligné.
    Jamais de dégradé, jamais d'ombre, angles vifs.
--}}
@props([
    'variante' => 'principal',
    'href' => null,
    'type' => 'submit',
    'pleineLargeur' => false,
])

@php
    $styles = [
        'principal' => 'h-12 px-8 bg-or text-noir hover:bg-or-clair',
        'secondaire' => 'h-12 px-8 border border-or text-or hover:border-or-clair hover:text-or-clair',
        'discret' => 'h-12 px-8 border border-filet text-ivoire hover:border-or-sombre',
        'lien' => 'text-ivoire underline decoration-or-sombre underline-offset-[6px] hover:decoration-or',
    ];

    $classes = [
        'etiquette inline-flex items-center justify-center gap-3 transition-colors duration-200 ease-alya',
        'disabled:cursor-not-allowed disabled:opacity-40 aria-disabled:pointer-events-none aria-disabled:opacity-40',
        $styles[$variante] ?? throw new InvalidArgumentException("Variante de bouton inconnue : {$variante}"),
        'w-full' => $pleineLargeur,
    ];
@endphp

@if ($href !== null)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
