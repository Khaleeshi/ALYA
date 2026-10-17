{{--
    État en pastille de texte (administration, espace livreur, disponibilité).
    succes : livrée, disponible · attention : stock faible, en attente ·
    alerte : rupture, annulée · neutre : tout le reste.
    Le carré de couleur porte l'état ; le texte reste ivoire pour être lisible.
--}}
@props(['etat' => 'neutre'])

@php
    $repere = [
        'succes' => 'bg-vert',
        'attention' => 'bg-ambre',
        'alerte' => 'bg-rouge',
        'neutre' => 'bg-gris',
    ][$etat] ?? throw new InvalidArgumentException("État inconnu : {$etat}");
@endphp

<span {{ $attributes->class('etiquette inline-flex items-center gap-2 border border-filet px-2.5 py-1 whitespace-nowrap text-ivoire') }}>
    <span class="size-2 {{ $repere }}" aria-hidden="true"></span>
    {{ $slot }}
</span>
