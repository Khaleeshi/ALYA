{{-- Zone de texte (adresse, indications pour le livreur, message). --}}
@props([
    'nom',
    'libelle',
    'valeur' => null,
    'lignes' => 3,
    'requis' => false,
    'erreur' => null,
])

@php
    $id = $attributes->get('id', 'champ-'.$nom);
    $erreur ??= $errors->first($nom);
@endphp

<div {{ $attributes->only('class')->class('flex flex-col gap-1.5') }}>
    <x-amani.libelle :pour="$id" :requis="$requis">{{ $libelle }}</x-amani.libelle>

    <textarea
        id="{{ $id }}"
        name="{{ $nom }}"
        rows="{{ $lignes }}"
        @required($requis)
        @if ($erreur) aria-invalid="true" aria-describedby="{{ $id }}-erreur" @endif
        {{ $attributes->except(['class', 'id'])->class([
            'w-full resize-y rounded-champ border bg-blanc px-4 py-3 text-base text-brun placeholder:text-taupe/70',
            'transition-colors focus:border-bronze focus:ring-2 focus:ring-nude-pale focus:outline-none',
            'border-rouge' => $erreur,
            'border-filet hover:border-nude' => ! $erreur,
        ]) }}
    >{{ old($nom, $valeur) }}</textarea>

    @if ($erreur)
        <x-amani.erreur :id="$id.'-erreur'" :message="$erreur" />
    @endif
</div>
