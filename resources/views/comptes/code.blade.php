{{-- Connexion, étape 2 : le code reçu par SMS. --}}
@php($numero = \App\Support\Telephone::afficher($telephone))

<x-layouts.compte :titre="__('comptes.code.titre')" :intro="__('comptes.code.intro', ['telephone' => $numero])">
    {{-- Recette seulement : aucun SMS ne part, le message s'affiche ici (config/services.php). --}}
    @if (session('recette.sms') && ! app()->isProduction())
        <x-amani.notice icone="info" class="mb-6">
            <strong class="text-brun">{{ __('comptes.code.recette') }}</strong> {{ session('recette.sms') }}
        </x-amani.notice>
    @endif

    <form method="POST" action="{{ route_langue('connexion.verifier') }}" class="flex flex-col gap-6" novalidate>
        @csrf

        <x-amani.champ
            nom="code"
            :libelle="__('comptes.code.code')"
            :requis="true"
            inputmode="numeric"
            autocomplete="one-time-code"
            maxlength="6"
            pattern="[0-9]*"
            placeholder="••••••"
            class="[&_input]:chiffres [&_input]:text-center [&_input]:text-2xl [&_input]:tracking-[0.5em]"
            autofocus
        />

        <x-amani.case nom="souvenir" :libelle="__('comptes.code.se_souvenir')" />

        <x-amani.bouton :pleine-largeur="true">{{ __('comptes.code.envoyer') }}</x-amani.bouton>
    </form>

    <div class="mt-6 flex flex-wrap items-center justify-between gap-4 border-t border-filet pt-6">
        <form method="POST" action="{{ route_langue('connexion.renvoyer') }}">
            @csrf
            <x-amani.bouton variante="lien">{{ __('comptes.code.renvoyer') }}</x-amani.bouton>
        </form>
        <x-amani.bouton variante="lien" :href="route_langue('connexion')">{{ __('comptes.code.changer') }}</x-amani.bouton>
    </div>
</x-layouts.compte>
