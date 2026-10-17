{{--
    <x-amani.ligne-panier> remplie à partir d'une ligne du panier
    (variante, produit et photos chargés : Panier::lignesCompletes()).
--}}
@props(['ligne'])

@php
    $variante = $ligne->variante;
    $produit = $variante->produit;
@endphp

<x-amani.ligne-panier
    :nom="$produit->nom"
    :teinte="$variante->nom"
    :quantite="$ligne->quantite"
    :prix="$ligne->prixUnitaire()"
    :photo="$variante->photo() ?? $produit->photoPrincipale()"
    :disponible="$ligne->estDisponible()"
    :url="$produit->estVisible() ? route_langue('boutique.produit', ['produit' => $produit->slug, 'variante' => $variante->id]) : null"
    :url-modifier="route_langue('panier.modifier', ['ligne' => $ligne->id])"
    :url-retirer="route_langue('panier.retirer', ['ligne' => $ligne->id])"
    :quantite-max="\App\Support\Panier\GestionPanier::quantiteMaximale($variante)"
    {{ $attributes }}
/>
