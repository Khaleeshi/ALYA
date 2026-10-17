{{-- Accueil provisoire : la vraie page d'accueil (maquette 1) arrive avec la phase Boutique. --}}
<x-layouts.vitrine>
    <section class="bg-gradient-to-br from-creme via-nude-pale to-nude">
        <div class="conteneur flex min-h-[60dvh] flex-col items-center justify-center gap-6 py-20 text-center">
            <p class="sur-titre text-taupe">{{ __('vitrine.accueil_page.sur_titre') }}</p>
            <h1 class="titre text-[44px] leading-[1.05] text-brun sm:text-6xl lg:text-7xl">
                {{ __('vitrine.signature_debut') }}
                <span class="manuscrit mt-2 block text-[64px] text-bronze sm:text-[88px] lg:text-[104px]">{{ __('vitrine.signature_fin') }}</span>
            </h1>
            <p class="text-lg text-taupe">{{ __('vitrine.accueil_page.texte') }}</p>
        </div>
    </section>
</x-layouts.vitrine>
