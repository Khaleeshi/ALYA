{{-- Accueil provisoire : la vraie page d'accueil arrive en phase 2 (CDC §5). --}}
<x-layouts.vitrine>
    <section class="conteneur flex min-h-[70dvh] flex-col items-center justify-center gap-8 py-24 text-center">
        <h1 class="titre-page text-5xl tracking-[0.2em] text-ivoire md:text-7xl">ALYA</h1>
        <p class="signature text-2xl text-or-clair md:text-3xl">{{ __('vitrine.signature') }}</p>
        <p class="etiquette text-gris">{{ __('vitrine.accueil_page.en_preparation') }}</p>
    </section>
</x-layouts.vitrine>
