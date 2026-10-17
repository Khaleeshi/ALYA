{{--
    Champ de saisie (maquette 5) : libellé au-dessus, fond blanc cassé, filet
    nude, coins de 8 px. Corps 16 px pour éviter le zoom automatique sur iPhone.
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
@endphp

<div {{ $attributes->only('class')->class('flex flex-col gap-1.5') }}>
    <x-amani.libelle :pour="$id" :requis="$requis">{{ $libelle }}</x-amani.libelle>

    <input
        id="{{ $id }}"
        name="{{ $nom }}"
        type="{{ $type }}"
        @if (old($nom, $valeur) !== null) value="{{ old($nom, $valeur) }}" @endif
        @required($requis)
        @if ($decrit !== '') aria-describedby="{{ $decrit }}" @endif
        @if ($erreur) aria-invalid="true" @endif
        {{ $attributes->except(['class', 'id'])->class([
            'h-12 w-full rounded-champ border bg-blanc px-4 text-base text-brun placeholder:text-taupe/70',
            'transition-colors duration-200 focus:border-bronze focus:ring-2 focus:ring-nude-pale focus:outline-none',
            'border-rouge' => $erreur,
            'border-filet hover:border-nude' => ! $erreur,
        ]) }}
    >

    @if ($aide)
        <p id="{{ $id }}-aide" class="text-sm text-taupe">{{ $aide }}</p>
    @endif

    @if ($erreur)
        <x-amani.erreur :id="$id.'-erreur'" :message="$erreur" />
    @endif
</div>
