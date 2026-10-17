<x-layouts.compte :titre="__('comptes.oubli.titre')" :intro="__('comptes.oubli.intro')">
    <form method="POST" action="{{ route_langue('mot-de-passe-oublie.envoyer') }}" class="flex flex-col gap-6" novalidate>
        @csrf

        <x-alya.champ nom="email" type="email" :libelle="__('comptes.oubli.email')" autocomplete="email" :requis="true" autofocus />

        <x-alya.bouton :pleine-largeur="true">{{ __('comptes.oubli.envoyer') }}</x-alya.bouton>

        <x-alya.bouton variante="lien" :href="route_langue('connexion')" class="self-start">{{ __('comptes.oubli.retour') }}</x-alya.bouton>
    </form>
</x-layouts.compte>
