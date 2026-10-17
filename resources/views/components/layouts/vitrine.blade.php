{{--
    Mise en page de la vitrine (maquettes) : bandeau de service, en-tête,
    contenu, bande de réassurance, pied de page, tiroir du panier.
    titre : titre de la page (complété par « · AMANI Cosmetics »).
    liens : menu principal ; par défaut, les pages existantes de config/boutique.php.
    reassurance : affiche la bande de réassurance au-dessus du pied de page.
--}}
@props([
    'titre' => null,
    'description' => null,
    'liens' => null,
    'reassurance' => true,
])

@use('App\Support\Langues')
@use('App\Support\Navigation')

@php
    $liens ??= Navigation::principale();
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#fffdfa">
    <title>{{ $titre ? $titre.' · '.__('vitrine.marque') : __('vitrine.marque') }}</title>
    @if ($description)
        <meta name="description" content="{{ $description }}">
    @endif

    @if (Langues::plusieurs() && request()->route()?->getName() && Str::startsWith(request()->route()->getName(), Langues::ouvertes()))
        @foreach (Langues::ouvertes() as $langue)
            <link rel="alternate" hreflang="{{ $langue }}" href="{{ Langues::pageEquivalente($langue) }}">
        @endforeach
        <link rel="alternate" hreflang="x-default" href="{{ Langues::pageEquivalente(Langues::PAR_DEFAUT) }}">
    @endif

    <x-amani.polices />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="flex min-h-dvh flex-col">
    <a href="#contenu" class="sr-only rounded-full bg-brun px-5 py-3 text-sm text-blanc focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[60]">{{ __('vitrine.aller_au_contenu') }}</a>

    <x-amani.bandeau-service :liens="Navigation::aide()" />
    <x-amani.en-tete :liens="$liens" />

    <main id="contenu" class="flex-1">
        {{ $slot }}
    </main>

    @if ($reassurance)
        <x-amani.bande-reassurance />
    @endif

    <x-amani.pied-de-page :liens="Navigation::principale()" :aide="Navigation::aide()" :reseaux="Navigation::reseaux()" />

    <x-amani.tiroir-panier />

    @livewireScripts
</body>
</html>
