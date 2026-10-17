{{--
    Tiroir du panier, depuis la droite. S'ouvre sur l'événement « ouvrir-panier »
    (bouton panier de l'en-tête, ajout d'un produit), se ferme par la croix, la
    touche Échap ou un clic à côté. Le focus reste dans le tiroir.

    Contenu : des <x-amani.ligne-panier> dans le slot ; le slot « pied » reçoit
    le sous-total et le bouton principal.
    evenement : nom de l'événement qui l'ouvre (un seul tiroir par événement).
    ouvert : ouvert dès l'affichage (juste après un ajout au panier).
    message : phrase en tête du tiroir (« … est dans votre panier »).
--}}
@props(['evenement' => 'ouvrir-panier', 'ouvert' => false, 'message' => null])

<div
    x-data="{ ouvert: @js((bool) $ouvert) }"
    x-on:{{ $evenement }}.window="ouvert = true"
    x-on:keydown.escape.window="ouvert = false"
    {{ $attributes }}
>
    <div
        x-show="ouvert"
        x-cloak
        x-transition.opacity.duration.300ms
        x-on:click="ouvert = false"
        class="fixed inset-0 z-40 bg-brun/40"
        aria-hidden="true"
    ></div>

    <aside
        x-show="ouvert"
        x-cloak
        x-trap.inert.noscroll="ouvert"
        x-transition:enter="transition-transform duration-300 ease-amani"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition-transform duration-300 ease-amani"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        role="dialog"
        aria-modal="true"
        aria-labelledby="{{ $evenement }}-titre"
        class="fixed inset-y-0 right-0 z-50 flex w-full max-w-md flex-col bg-page shadow-flottant"
    >
        <header class="flex h-16 shrink-0 items-center justify-between border-b border-filet px-6">
            <h2 id="{{ $evenement }}-titre" class="titre text-2xl text-brun">{{ __('vitrine.panier.titre') }}</h2>
            <button type="button" x-on:click="ouvert = false" class="-mr-2 inline-flex size-10 items-center justify-center rounded-full text-brun hover:bg-nude-pale" aria-label="{{ __('vitrine.fermer') }}">
                <x-amani.icone nom="fermer" />
            </button>
        </header>

        <div class="flex-1 overflow-y-auto px-6 py-6">
            @if ($message)
                <p class="mb-5 flex items-start gap-2 rounded-champ bg-nude-pale px-4 py-3 text-sm text-brun" role="status">
                    <x-amani.icone nom="coche" :taille="18" class="mt-px shrink-0 text-vert" />
                    {{ $message }}
                </p>
            @endif
            @if ($slot->isEmpty())
                <p class="text-taupe">{{ __('vitrine.panier.vide') }}</p>
            @else
                <ul>{{ $slot }}</ul>
            @endif
        </div>

        @isset($pied)
            <footer class="shrink-0 border-t border-filet bg-blanc px-6 py-6">{{ $pied }}</footer>
        @endisset
    </aside>
</div>
