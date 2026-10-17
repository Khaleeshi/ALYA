{{--
    Écrans de compte : une carte blanche centrée, un titre en Playfair.
--}}
@props(['titre', 'intro' => null])

<x-layouts.vitrine :titre="$titre" :reassurance="false">
    <div class="conteneur flex justify-center py-12 lg:py-20">
        <div class="w-full max-w-md rounded-bloc bg-blanc p-6 shadow-carte sm:p-10">
            <h1 class="titre text-[32px] text-brun">{{ $titre }}</h1>

            @if ($intro)
                <p class="mt-3 text-[15px] text-taupe">{{ $intro }}</p>
            @endif

            @if (session('statut'))
                <x-amani.notice icone="coche" class="mt-6" role="status">{{ session('statut') }}</x-amani.notice>
            @endif

            <div class="mt-8">
                {{ $slot }}
            </div>
        </div>
    </div>
</x-layouts.vitrine>
