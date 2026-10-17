{{--
    Mise en page de la vitrine : en-tête, contenu, pied de page, tiroir du panier.
    titre : titre de la page (complété par « · ALYA Cosmetic »).
    liens : navigation principale, sept entrées au plus.
--}}
@props([
    'titre' => null,
    'description' => null,
    'liens' => [],
])

@use('App\Support\Langues')

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#070707">
    <title>{{ $titre ? $titre.' · '.__('vitrine.marque') : __('vitrine.marque') }}</title>
    @if ($description)
        <meta name="description" content="{{ $description }}">
    @endif

    @if (request()->route()?->getName() && Str::startsWith(request()->route()->getName(), Langues::DISPONIBLES))
        @foreach (Langues::DISPONIBLES as $langue)
            <link rel="alternate" hreflang="{{ $langue }}" href="{{ Langues::pageEquivalente($langue) }}">
        @endforeach
        <link rel="alternate" hreflang="x-default" href="{{ Langues::pageEquivalente(Langues::PAR_DEFAUT) }}">
    @endif

    <x-alya.polices />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="flex min-h-dvh flex-col">
    <a href="#contenu" class="etiquette sr-only bg-or px-4 py-3 text-noir focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[60]">{{ __('vitrine.aller_au_contenu') }}</a>

    <x-alya.en-tete :liens="$liens" />

    <main id="contenu" class="flex-1">
        {{ $slot }}
    </main>

    <x-alya.pied-de-page />

    <x-alya.tiroir-panier />

    @livewireScripts
</body>
</html>
