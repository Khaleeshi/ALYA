{{--
    Champ de saisie : libellé en étiquette, fond noir relief, filet de 1 px,
    angles de 2 px. Corps 16 px pour éviter le zoom automatique sur iPhone.
    L'erreur s'affiche en ivoire avec un repère rouge : le rouge seul ne serait
    pas assez lisible sur noir (contraste 3:1).
--}}
@props([
    'nom',
    'libelle',
    'type' => 'text',
    'valeur' => null,
    'aide' => null,
    'requis' => false,
    'erreur' => null,
])

@php
    $id = $attributes->get('id', 'champ-'.str_replace(['[', ']', '.'], '-', $nom));
    $erreur ??= $errors->first($nom);
    $decrit = collect([$aide ? $id.'-aide' : null, $erreur ? $id.'-erreur' : null])->filter()->implode(' ');
    $valeurAffichee = $type === 'password' ? null : old($nom, $valeur);
@endphp

<div {{ $attributes->only('class')->class('flex flex-col gap-2') }}>
    <label for="{{ $id }}" class="etiquette text-gris">
        {{ $libelle }}
        @unless ($requis)
            <span class="normal-case tracking-normal">({{ __('vitrine.facultatif') }})</span>
        @endunless
    </label>

    <input
        id="{{ $id }}"
        name="{{ $nom }}"
        type="{{ $type }}"
        @if ($valeurAffichee !== null) value="{{ $valeurAffichee }}" @endif
        @required($requis)
        @if ($decrit !== '') aria-describedby="{{ $decrit }}" @endif
        @if ($erreur) aria-invalid="true" @endif
        {{ $attributes->except(['class', 'id'])->class([
            'h-12 w-full rounded-champ border bg-relief px-4 text-base text-ivoire placeholder:text-gris',
            'transition-colors duration-200 focus:border-or focus:outline-none',
            'border-rouge' => $erreur,
            'border-filet hover:border-or-sombre' => ! $erreur,
        ]) }}
    >

    @if ($aide)
        <p id="{{ $id }}-aide" class="text-sm text-gris">{{ $aide }}</p>
    @endif

    @if ($erreur)
        <p id="{{ $id }}-erreur" class="flex items-start gap-2 text-sm text-ivoire">
            <x-alya.icone nom="alerte" :taille="16" class="mt-0.5 text-rouge" />
            <span>{{ $erreur }}</span>
        </p>
    @endif
</div>
