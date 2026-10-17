<x-layouts.compte :titre="__('comptes.inscription.titre')" :intro="__('comptes.inscription.intro')">
    <form method="POST" action="{{ route_langue('inscription.envoyer') }}" class="flex flex-col gap-6" novalidate>
        @csrf

        <div class="grid gap-6 sm:grid-cols-2">
            <x-alya.champ nom="prenom" :libelle="__('comptes.inscription.prenom')" autocomplete="given-name" :requis="true" />
            <x-alya.champ nom="nom" :libelle="__('comptes.inscription.nom')" autocomplete="family-name" :requis="true" />
        </div>

        <x-alya.champ
            nom="telephone"
            type="tel"
            :libelle="__('comptes.inscription.telephone')"
            :aide="__('comptes.inscription.telephone_aide')"
            autocomplete="tel"
            :requis="true"
        />

        <x-alya.champ
            nom="email"
            type="email"
            :libelle="__('comptes.inscription.email')"
            :aide="__('comptes.inscription.email_aide')"
            autocomplete="email"
            :requis="true"
        />

        <x-alya.champ
            nom="password"
            type="password"
            :libelle="__('comptes.inscription.mot_de_passe')"
            :aide="__('comptes.inscription.mot_de_passe_aide')"
            autocomplete="new-password"
            :requis="true"
        />

        <x-alya.champ
            nom="password_confirmation"
            type="password"
            :libelle="__('comptes.inscription.confirmation')"
            autocomplete="new-password"
            :requis="true"
        />

        <div class="flex flex-col gap-4 border-t border-filet pt-6">
            <p class="text-sm text-gris">{{ __('comptes.inscription.messages_commande') }}</p>
            <x-alya.case nom="offres" :libelle="__('comptes.inscription.offres')" />
        </div>

        <x-alya.bouton :pleine-largeur="true">{{ __('comptes.inscription.envoyer') }}</x-alya.bouton>
    </form>

    <p class="mt-10 text-sm text-gris">
        {{ __('comptes.inscription.deja_un_compte') }}
        <x-alya.bouton variante="lien" :href="route_langue('connexion')" class="ml-2">{{ __('comptes.inscription.se_connecter') }}</x-alya.bouton>
    </p>
</x-layouts.compte>
