{{--
    Écrans de compte : une colonne étroite, un titre, un seul bouton doré.
--}}
@props(['titre', 'intro' => null])

<x-layouts.vitrine :titre="$titre">
    <div class="conteneur flex justify-center py-16 lg:py-24">
        <div class="w-full max-w-md">
            <h1 class="titre-page text-3xl text-ivoire lg:text-4xl">{{ $titre }}</h1>

            @if ($intro)
                <p class="mt-4 text-gris">{{ $intro }}</p>
            @endif

            @if (session('statut'))
                <p class="mt-8 border-l border-or py-2 pl-4 text-sm text-ivoire" role="status">{{ session('statut') }}</p>
            @endif

            <div class="mt-10">
                {{ $slot }}
            </div>
        </div>
    </div>
</x-layouts.vitrine>
