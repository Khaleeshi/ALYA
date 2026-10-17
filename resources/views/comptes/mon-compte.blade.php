{{--
    Tableau de bord de l'espace client (maquette 8). Les cartes commandes,
    favoris, points fidélité et assistance apparaîtront avec leurs pages.
--}}
@use('App\Support\Navigation')
@use('App\Support\Telephone')

@php
    $cliente = auth('web')->user();
    $whatsapp = Navigation::reseaux()['WhatsApp'] ?? null;
    // « AMANI » reste en lettres de titre au milieu de la phrase manuscrite, pour rester lisible.
    [$merciAvant, $merciApres] = explode('|', __('comptes.espace.merci', ['marque' => '|']), 2);
@endphp

<x-layouts.espace-client :titre="__('comptes.mon_compte.titre')">
    <section class="relative overflow-hidden rounded-bloc bg-gradient-to-r from-creme via-nude-pale to-nude px-6 py-8 sm:px-10 sm:py-10">
        <h1 class="titre flex items-center gap-2 text-[32px] text-brun sm:text-[40px]">
            {{ __('comptes.espace.bienvenue') }}, <span class="font-medium">{{ $cliente->prenom }}</span>
            <x-amani.icone nom="couronne" :taille="26" class="text-or" />
        </h1>
        <p class="mt-2 text-lg text-brun/85">{{ __('comptes.espace.accroche') }}</p>
        <p class="manuscrit mt-5 text-[30px] text-bronze">{{ $merciAvant }}<span class="titre text-[20px] tracking-[0.1em]">AMANI</span>{{ $merciApres }}</p>
    </section>

    <x-slot:cote>
        <x-amani.bloc :titre="__('comptes.profil.titre')" icone="compte" :lien="Route::has(app()->getLocale().'.mon-profil') ? route_langue('mon-profil') : null" :libelle-lien="__('comptes.profil.modifier')">
            <div class="flex items-center gap-4">
                <x-amani.avatar :nom="$cliente->nom_complet" taille="grande" />
                <dl class="min-w-0 text-sm">
                    <dt class="sr-only">{{ __('comptes.mon_compte.coordonnees') }}</dt>
                    <dd class="truncate font-semibold text-brun">{{ $cliente->nom_complet }}</dd>
                    <dd class="truncate text-taupe">{{ $cliente->email ?? __('comptes.mon_compte.non_renseigne') }}</dd>
                    <dd class="chiffres text-taupe">{{ Telephone::afficher($cliente->telephone) }}</dd>
                </dl>
            </div>
        </x-amani.bloc>

        @if ($whatsapp)
            <section class="flex gap-4 rounded-carte bg-gradient-to-br from-creme to-nude-pale p-5 shadow-carte">
                <x-amani.icone nom="casque" :taille="34" class="text-bronze" />
                <div>
                    <h2 class="text-[15px] font-semibold text-brun">{{ __('comptes.espace.aide_titre') }}</h2>
                    <p class="mt-1 text-sm text-taupe">{{ __('comptes.espace.aide_texte') }}</p>
                    <x-amani.bouton :href="$whatsapp" taille="petite" icone-apres="fleche" class="mt-3" target="_blank" rel="noopener">{{ __('comptes.espace.aide_bouton') }}</x-amani.bouton>
                </div>
            </section>
        @endif
    </x-slot:cote>
</x-layouts.espace-client>
