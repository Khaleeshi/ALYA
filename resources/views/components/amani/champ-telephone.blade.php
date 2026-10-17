{{--
    Numéro de téléphone sénégalais (maquette 5) : drapeau et +221 devant, la
    cliente tape seulement ses 9 chiffres. Le serveur enregistre +221XXXXXXXXX.
--}}
@props([
    'nom' => 'telephone',
    'libelle',
    'valeur' => null,
    'aide' => null,
    'requis' => true,
    'erreur' => null,
])

@php
    $id = $attributes->get('id', 'champ-'.$nom);
    $erreur ??= $errors->first($nom);
    $decrit = collect([$aide ? $id.'-aide' : null, $erreur ? $id.'-erreur' : null])->filter()->implode(' ');
    $valeur = old($nom, $valeur);

    if (is_string($valeur) && str_starts_with($valeur, '+221')) {
        $valeur = trim(substr(\App\Support\Telephone::afficher($valeur), 4));
    }
@endphp

<div {{ $attributes->only('class')->class('flex flex-col gap-1.5') }}>
    <x-amani.libelle :pour="$id" :requis="$requis">{{ $libelle }}</x-amani.libelle>

    <div @class([
        'flex h-12 overflow-hidden rounded-champ border bg-blanc transition-colors focus-within:border-bronze focus-within:ring-2 focus-within:ring-nude-pale',
        'border-rouge' => $erreur,
        'border-filet hover:border-nude' => ! $erreur,
    ])>
        <span class="flex shrink-0 items-center gap-2 border-r border-filet bg-creme px-3 text-[15px] font-medium text-brun">
            <x-amani.drapeau-senegal />
            +221
        </span>
        <input
            id="{{ $id }}"
            name="{{ $nom }}"
            type="tel"
            inputmode="tel"
            autocomplete="tel-national"
            placeholder="77 123 45 67"
            @if ($valeur !== null) value="{{ $valeur }}" @endif
            @required($requis)
            @if ($decrit !== '') aria-describedby="{{ $decrit }}" @endif
            @if ($erreur) aria-invalid="true" @endif
            {{ $attributes->except(['class', 'id'])->class('chiffres min-w-0 flex-1 bg-transparent px-3 text-base text-brun placeholder:text-taupe/70 focus:outline-none') }}
        >
    </div>

    @if ($aide)
        <p id="{{ $id }}-aide" class="text-sm text-taupe">{{ $aide }}</p>
    @endif

    @if ($erreur)
        <x-amani.erreur :id="$id.'-erreur'" :message="$erreur" />
    @endif
</div>
