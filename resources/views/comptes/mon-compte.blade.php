{{-- Espace cliente : coordonnées pour l'instant ; commandes, favoris et VIP arrivent avec les phases 2 à 5. --}}
@php($cliente = auth('web')->user())

<x-layouts.compte :titre="__('comptes.mon_compte.titre')" :intro="__('comptes.mon_compte.bonjour', ['prenom' => $cliente->prenom])">
    <section>
        <h2 class="etiquette mb-4 text-gris">{{ __('comptes.mon_compte.coordonnees') }}</h2>
        <dl class="divide-y divide-filet border-y border-filet text-sm">
            <div class="flex justify-between gap-6 py-3">
                <dt class="text-gris">{{ __('comptes.mon_compte.telephone') }}</dt>
                <dd class="chiffres text-ivoire">{{ \App\Support\Telephone::afficher($cliente->telephone) }}</dd>
            </div>
            <div class="flex justify-between gap-6 py-3">
                <dt class="text-gris">{{ __('comptes.mon_compte.email') }}</dt>
                <dd class="truncate text-ivoire">{{ $cliente->email }}</dd>
            </div>
            <div class="flex justify-between gap-6 py-3">
                <dt class="text-gris">{{ __('comptes.mon_compte.langue') }}</dt>
                <dd class="text-ivoire">{{ $cliente->langue === 'en' ? 'English' : 'Français' }}</dd>
            </div>
        </dl>
    </section>

    <form method="POST" action="{{ route_langue('deconnexion') }}" class="mt-10">
        @csrf
        <x-alya.bouton variante="discret" :pleine-largeur="true">{{ __('comptes.mon_compte.deconnexion') }}</x-alya.bouton>
    </form>
</x-layouts.compte>
