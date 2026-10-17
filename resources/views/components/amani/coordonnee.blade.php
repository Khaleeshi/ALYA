{{--
    Ligne de coordonnées (maquette 7 : service client, WhatsApp, email,
    adresse) : icône bronze, titre, puis le texte dans le slot.
    Coordonnées réelles uniquement (cahier §13).
--}}
@props(['icone', 'titre'])

<div {{ $attributes->class('flex gap-4') }}>
    <x-amani.icone :nom="$icone" :taille="28" class="mt-0.5 text-bronze" />
    <div class="text-sm text-taupe">
        <p class="font-semibold text-brun">{{ $titre }}</p>
        {{ $slot }}
    </div>
</div>
