{{--
    État en pastille (commande, stock, livraison) : un point de couleur et le
    texte dans la même couleur, lisible sur fond clair.
    succes : livrée, en stock · attention : stock faible, en attente ·
    alerte : rupture, annulée · neutre : tout le reste.
--}}
@props(['etat' => 'neutre'])

@php
    [$texte, $fond, $point] = [
        'succes' => ['text-vert', 'bg-vert/10', 'bg-vert'],
        'attention' => ['text-ambre', 'bg-ambre/10', 'bg-ambre'],
        'alerte' => ['text-rouge', 'bg-rouge/10', 'bg-rouge'],
        'neutre' => ['text-taupe', 'bg-nude-pale', 'bg-taupe'],
    ][$etat] ?? throw new InvalidArgumentException("État inconnu : {$etat}");
@endphp

<span {{ $attributes->class("inline-flex items-center gap-2 rounded-full px-3 py-1 text-[13px] font-medium whitespace-nowrap {$texte} {$fond}") }}>
    <span class="size-2 rounded-full {{ $point }}" aria-hidden="true"></span>
    {{ $slot }}
</span>
