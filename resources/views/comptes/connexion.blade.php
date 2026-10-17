<x-layouts.compte :titre="__('comptes.connexion.titre')" :intro="__('comptes.connexion.intro')">
    <form method="POST" action="{{ route_langue('connexion.envoyer') }}" class="flex flex-col gap-6" novalidate>
        @csrf

        <x-alya.champ
            nom="identifiant"
            :libelle="__('comptes.connexion.identifiant')"
            autocomplete="username"
            inputmode="email"
            :requis="true"
            autofocus
        />

        <x-alya.champ
            nom="password"
            type="password"
            :libelle="__('comptes.connexion.mot_de_passe')"
            autocomplete="current-password"
            :requis="true"
        />

        <x-alya.case nom="remember" :libelle="__('comptes.connexion.se_souvenir')" />

        <x-alya.bouton :pleine-largeur="true">{{ __('comptes.connexion.envoyer') }}</x-alya.bouton>

        <x-alya.bouton variante="lien" :href="route_langue('mot-de-passe-oublie')" class="self-start">
            {{ __('comptes.connexion.oublie') }}
        </x-alya.bouton>
    </form>

    <div class="mt-12 border-t border-filet pt-8">
        <p class="text-sm text-gris">{{ __('comptes.connexion.pas_de_compte') }}</p>
        <x-alya.bouton variante="discret" :href="route_langue('inscription')" :pleine-largeur="true" class="mt-4">
            {{ __('comptes.connexion.creer') }}
        </x-alya.bouton>
    </div>
</x-layouts.compte>
