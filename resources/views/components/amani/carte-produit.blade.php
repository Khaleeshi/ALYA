{{--
    Vignette d'un produit du catalogue : <x-amani.vignette-produit> remplie
    à partir du modèle. Les variantes et les photos doivent être chargées
    (with(['variantes', 'media'])).
    Badge : « Épuisé », sinon « Édition limitée », sinon « Nouveau ».
    Bouton : vers la fiche, en attendant le panier.
--}}
@props(['produit'])

@php
    $photos = $produit->getMedia(\App\Models\Produit::PHOTOS);
    $url = route_langue('boutique.produit', ['produit' => $produit->slug]);
    $badge = match (true) {
        ! $produit->enStock() => ['texte' => __('vitrine.produit.epuise'), 'ton' => 'clair'],
        $produit->edition_limitee => ['texte' => __('vitrine.produit.edition_limitee'), 'ton' => 'bronze'],
        $produit->estNouveau() => ['texte' => __('vitrine.produit.nouveau'), 'ton' => 'bronze'],
        default => null,
    };
@endphp

<x-amani.vignette-produit
    :nom="$produit->nom"
    :url="$url"
    :prix="$produit->prixMinimum()"
    :a-partir-de="$produit->aPlusieursPrix()"
    :photo="$photos->first()"
    :photo-survol="$photos->get(1)"
    :badge="$badge"
    {{ $attributes }}
>
    <x-slot:action>
        <x-amani.bouton :href="$url" variante="secondaire" taille="petite" :pleine-largeur="true">
            {{ $produit->estCoffret() ? __('vitrine.coffrets.voir') : __('vitrine.produit.voir') }}
        </x-amani.bouton>
    </x-slot:action>
</x-amani.vignette-produit>
