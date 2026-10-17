<x-layouts.compte :titre="__('comptes.reinitialisation.titre')">
    <form method="POST" action="{{ route_langue('reinitialiser-mot-de-passe.envoyer') }}" class="flex flex-col gap-6" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-alya.champ
            nom="email"
            type="email"
            :libelle="__('comptes.reinitialisation.email')"
            :valeur="$request->query('email')"
            autocomplete="email"
            :requis="true"
        />

        <x-alya.champ
            nom="password"
            type="password"
            :libelle="__('comptes.reinitialisation.mot_de_passe')"
            :aide="__('comptes.inscription.mot_de_passe_aide')"
            autocomplete="new-password"
            :requis="true"
            autofocus
        />

        <x-alya.champ
            nom="password_confirmation"
            type="password"
            :libelle="__('comptes.reinitialisation.confirmation')"
            autocomplete="new-password"
            :requis="true"
        />

        <x-alya.bouton :pleine-largeur="true">{{ __('comptes.reinitialisation.envoyer') }}</x-alya.bouton>
    </form>
</x-layouts.compte>
