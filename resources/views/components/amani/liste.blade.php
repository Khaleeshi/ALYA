{{--
    Liste déroulante (région, zone…). options : [valeur => libellé].
--}}
@props([
    'nom',
    'libelle',
    'options' => [],
    'valeur' => null,
    'invite' => null,
    'requis' => false,
    'erreur' => null,
])

@php
    $id = $attributes->get('id', 'champ-'.$nom);
    $erreur ??= $errors->first($nom);
    $choisi = (string) old($nom, $valeur);
@endphp

<div {{ $attributes->only('class')->class('flex flex-col gap-1.5') }}>
    <x-amani.libelle :pour="$id" :requis="$requis">{{ $libelle }}</x-amani.libelle>

    <div class="relative">
        <select
            id="{{ $id }}"
            name="{{ $nom }}"
            @required($requis)
            @if ($erreur) aria-invalid="true" aria-describedby="{{ $id }}-erreur" @endif
            {{ $attributes->except(['class', 'id'])->class([
                'h-12 w-full cursor-pointer appearance-none rounded-champ border bg-blanc pr-11 pl-4 text-base text-brun',
                'transition-colors focus:border-bronze focus:ring-2 focus:ring-nude-pale focus:outline-none',
                'border-rouge' => $erreur,
                'border-filet hover:border-nude' => ! $erreur,
            ]) }}
        >
            @if ($invite)
                <option value="" @selected($choisi === '')>{{ $invite }}</option>
            @endif
            @foreach ($options as $cle => $texte)
                <option value="{{ $cle }}" @selected($choisi === (string) $cle)>{{ $texte }}</option>
            @endforeach
        </select>
        <x-amani.icone nom="chevron-bas" :taille="18" class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-brun" />
    </div>

    @if ($erreur)
        <x-amani.erreur :id="$id.'-erreur'" :message="$erreur" />
    @endif
</div>
