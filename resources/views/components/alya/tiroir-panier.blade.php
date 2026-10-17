{{--
    Tiroir du panier, depuis la droite, en 320 ms (audit 9.3). S'ouvre sur
    l'événement « ouvrir-panier » (bouton sac de l'en-tête, ajout d'un produit),
    se ferme par la croix, la touche Échap ou un clic à côté. Le focus reste
    dans le tiroir tant qu'il est ouvert.

    Contenu : des <x-alya.ligne-panier> dans le slot ; le slot « pied » reçoit
    le sous-total et le bouton principal.
    evenement : nom de l'événement qui l'ouvre (un seul tiroir par événement).
--}}
@props(['evenement' => 'ouvrir-panier'])

<div
    x-data="{ ouvert: false }"
    x-on:{{ $evenement }}.window="ouvert = true"
    x-on:keydown.escape.window="ouvert = false"
    {{ $attributes }}
>
    <div
        x-show="ouvert"
        x-cloak
        x-transition.opacity.duration.320ms
        x-on:click="ouvert = false"
        class="fixed inset-0 z-40 bg-noir/80"
        aria-hidden="true"
    ></div>

    <aside
        x-show="ouvert"
        x-cloak
        x-trap.inert.noscroll="ouvert"
        x-transition:enter="transition-transform duration-[320ms] ease-alya"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition-transform duration-[320ms] ease-alya"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        role="dialog"
        aria-modal="true"
        aria-labelledby="{{ $evenement }}-titre"
        class="fixed inset-y-0 right-0 z-50 flex w-full max-w-md flex-col border-l border-filet bg-surface"
    >
        <header class="flex h-16 shrink-0 items-center justify-between border-b border-filet px-6">
            <h2 id="{{ $evenement }}-titre" class="etiquette text-ivoire">{{ __('vitrine.panier.titre') }}</h2>
            <button type="button" x-on:click="ouvert = false" class="-mr-2 inline-flex size-10 items-center justify-center text-ivoire hover:text-or" aria-label="{{ __('vitrine.fermer') }}">
                <x-alya.icone nom="fermer" />
            </button>
        </header>

        <div class="flex-1 overflow-y-auto px-6 py-6">
            @if ($slot->isEmpty())
                <p class="text-gris">{{ __('vitrine.panier.vide') }}</p>
            @else
                <ul>{{ $slot }}</ul>
            @endif
        </div>

        @isset($pied)
            <footer class="shrink-0 border-t border-filet px-6 py-6">{{ $pied }}</footer>
        @endisset
    </aside>
</div>
