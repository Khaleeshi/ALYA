{{--
    Espace client (maquette 8) : menu à gauche (avatar, nom, ancienneté,
    rubriques, carte de la marque, déconnexion), contenu au centre, colonne
    facultative à droite (slot « cote »).
--}}
@props(['titre'])

@use('App\Support\Navigation')

@php($cliente = auth('web')->user())

<x-layouts.vitrine :titre="$titre" :reassurance="false">
    <div class="conteneur grid gap-6 py-8 lg:grid-cols-[260px_1fr] lg:gap-8 lg:py-12 @isset($cote) xl:grid-cols-[260px_1fr_340px] @endisset">
        <aside class="flex flex-col gap-6">
            <div class="flex items-center gap-4">
                <x-amani.avatar :nom="$cliente->nom_complet" taille="grande" />
                <div class="min-w-0">
                    <p class="flex items-center gap-1.5 truncate text-[17px] font-semibold text-brun">
                        {{ $cliente->nom_complet }}
                        <x-amani.icone nom="couronne" :taille="16" class="text-or" />
                    </p>
                    <p class="text-[13px] text-taupe">{{ __('comptes.espace.membre_depuis', ['date' => $cliente->created_at->translatedFormat('j F Y')]) }}</p>
                </div>
            </div>

            <x-amani.menu-lateral :liens="Navigation::espaceClient()" :libelle="__('comptes.espace.titre_menu')" />

            <div class="hidden flex-col items-center gap-3 rounded-carte bg-gradient-to-br from-creme via-nude-pale to-nude px-5 py-8 text-center lg:flex">
                <x-amani.logo />
                <p class="titre text-lg text-brun italic">{{ __('vitrine.signature') }}</p>
            </div>

            <form method="POST" action="{{ route_langue('deconnexion') }}">
                @csrf
                <button type="submit" class="flex w-full items-center gap-3 rounded-champ border border-filet bg-blanc px-4 py-3 text-[15px] text-brun transition-colors hover:bg-nude-pale">
                    <x-amani.icone nom="deconnexion" :taille="22" class="text-taupe" />
                    {{ __('comptes.mon_compte.deconnexion') }}
                </button>
            </form>
        </aside>

        <div class="flex min-w-0 flex-col gap-6">
            @if (session('statut'))
                <x-amani.notice icone="coche" role="status">{{ session('statut') }}</x-amani.notice>
            @endif

            {{ $slot }}
        </div>

        @isset($cote)
            <div class="flex flex-col gap-6 lg:col-start-2 xl:col-start-auto">{{ $cote }}</div>
        @endisset
    </div>
</x-layouts.vitrine>
