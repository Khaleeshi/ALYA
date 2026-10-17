{{-- Espace cliente : coordonnées pour l'instant ; commandes, favoris, points et retours arrivent ensuite (cahier §11). --}}
@php($cliente = auth('web')->user())

<x-layouts.compte :titre="__('comptes.mon_compte.titre')" :intro="__('comptes.mon_compte.bonjour', ['prenom' => $cliente->prenom])">
    <section>
        <h2 class="mb-3 text-[15px] font-semibold text-brun">{{ __('comptes.mon_compte.coordonnees') }}</h2>
        <dl class="divide-y divide-filet rounded-champ border border-filet text-sm">
            <div class="flex justify-between gap-6 px-4 py-3">
                <dt class="text-taupe">{{ __('comptes.mon_compte.telephone') }}</dt>
                <dd class="chiffres font-medium text-brun">{{ \App\Support\Telephone::afficher($cliente->telephone) }}</dd>
            </div>
            <div class="flex justify-between gap-6 px-4 py-3">
                <dt class="text-taupe">{{ __('comptes.mon_compte.email') }}</dt>
                <dd class="truncate font-medium text-brun">{{ $cliente->email ?? __('comptes.mon_compte.non_renseigne') }}</dd>
            </div>
        </dl>
    </section>

    <form method="POST" action="{{ route_langue('deconnexion') }}" class="mt-8">
        @csrf
        <x-amani.bouton variante="secondaire" :pleine-largeur="true">{{ __('comptes.mon_compte.deconnexion') }}</x-amani.bouton>
    </form>
</x-layouts.compte>
