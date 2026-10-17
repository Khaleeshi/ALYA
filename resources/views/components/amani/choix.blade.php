{{--
    Choix en carte (maquette 5) : créneaux de livraison, moyen de paiement.
    Un groupe de <x-amani.choix> de même nom forme un choix unique.
    Le slot remplace le libellé (logo d'un moyen de paiement, par exemple).
--}}
@props(['nom', 'valeur', 'libelle' => null, 'coche' => false, 'desactive' => false])

@php
    $id = $attributes->get('id', 'choix-'.$nom.'-'.\Illuminate\Support\Str::slug((string) $valeur));
    $estCoche = (string) old($nom, $coche ? $valeur : null) === (string) $valeur;
@endphp

<label for="{{ $id }}" {{ $attributes->only('class')->class([
    'group relative flex cursor-pointer items-center gap-3 rounded-champ border px-4 py-3.5 text-[15px] transition-colors',
    'border-filet bg-blanc hover:border-nude has-[:checked]:border-bronze has-[:checked]:bg-nude-pale',
    'pointer-events-none opacity-50' => $desactive,
]) }}>
    <input
        id="{{ $id }}"
        name="{{ $nom }}"
        type="radio"
        value="{{ $valeur }}"
        @checked($estCoche)
        @disabled($desactive)
        {{ $attributes->except(['class', 'id'])->class('peer size-[18px] shrink-0 cursor-pointer appearance-none rounded-full border border-taupe bg-blanc transition checked:border-[5px] checked:border-bronze') }}
    >
    <span class="text-brun">{{ $slot->isEmpty() ? $libelle : $slot }}</span>
</label>
