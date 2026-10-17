{{--
    Mon panier (cahier §6.1, maquette 4) : étapes de la commande, articles
    avec quantité et suppression, résumé (sous-total, livraison à calculer,
    total estimé), suggestions.

    Le code promotionnel, l'estimation par zone et le programme VIP
    s'afficheront avec les promotions, les zones de livraison et les règles
    VIP (cahier §21). Le bouton « Passer à la livraison » arrive avec la
    commande.
--}}
<x-layouts.vitrine :titre="__('vitrine.panier.titre')">
    <x-amani.bandeau-page :titre="__('vitrine.panier.titre')" :sous-titre="__('vitrine.panier.sous_titre')" />

    <div class="conteneur py-8 lg:py-12">
        <x-amani.etapes :courante="1" class="mx-auto max-w-3xl" />

        @if (session('panier.message') || $ajustes !== [] || session('panier.erreur'))
            <div class="mt-8 flex flex-col gap-2" role="status">
                @if (session('panier.erreur'))
                    <x-amani.notice icone="alerte">{{ session('panier.erreur') }}</x-amani.notice>
                @endif
                @if (session('panier.message'))
                    <x-amani.notice icone="coche">{{ session('panier.message') }}</x-amani.notice>
                @endif
                @foreach ($ajustes as $nom)
                    <x-amani.notice icone="alerte">{{ __('vitrine.panier.ajuste', ['nom' => $nom]) }}</x-amani.notice>
                @endforeach
            </div>
        @endif

        @if ($lignes->isEmpty())
            <div class="mt-10 flex flex-col items-center gap-5 rounded-bloc bg-nude-pale/60 px-6 py-14 text-center">
                <x-amani.icone nom="sac" :taille="40" class="text-bronze" />
                <p class="text-lg text-brun">{{ __('vitrine.panier.vide') }}</p>
                <x-amani.bouton :href="route_langue('boutique')" icone-apres="fleche">{{ __('vitrine.panier.decouvrir') }}</x-amani.bouton>
            </div>
        @else
            <div class="mt-10 grid gap-8 lg:grid-cols-[minmax(0,1fr)_380px] lg:gap-10">
                <section aria-labelledby="titre-articles">
                    <div class="flex items-center justify-between gap-4 border-b border-filet pb-4">
                        <h2 id="titre-articles" class="titre text-[28px] text-brun">{{ __('vitrine.panier.mes_produits', ['nombre' => $lignes->sum('quantite')]) }}</h2>
                        <form method="post" action="{{ route_langue('panier.vider') }}">
                            @csrf
                            @method('delete')
                            <button type="submit" class="inline-flex items-center gap-2 text-sm text-taupe hover:text-rouge">
                                <x-amani.icone nom="corbeille" :taille="18" />
                                {{ __('vitrine.panier.vider') }}
                            </button>
                        </form>
                    </div>

                    <ul class="pt-5">
                        @foreach ($lignes as $ligne)
                            <x-amani.ligne-panier-produit :ligne="$ligne" />
                        @endforeach
                    </ul>

                    <a href="{{ route_langue('boutique') }}" class="mt-4 inline-flex items-center gap-2 text-[15px] text-brun underline decoration-or underline-offset-4 hover:text-bronze-fonce">
                        <x-amani.icone nom="fleche" :taille="18" class="rotate-180" />
                        {{ __('vitrine.panier.continuer') }}
                    </a>
                </section>

                <aside class="flex flex-col gap-5 self-start lg:sticky lg:top-28" aria-labelledby="titre-resume">
                    <div class="rounded-carte bg-blanc p-6 shadow-carte">
                        <h2 id="titre-resume" class="titre text-2xl text-brun">{{ __('vitrine.panier.resume') }}</h2>
                        <dl class="mt-5 flex flex-col gap-3 text-[15px]">
                            <div class="flex items-baseline justify-between gap-4">
                                <dt class="text-taupe">{{ trans_choice('vitrine.panier.sous_total_detail', $nombre, ['nombre' => $nombre]) }}</dt>
                                <dd><x-amani.prix :montant="$sousTotal" /></dd>
                            </div>
                            <div class="flex items-baseline justify-between gap-4">
                                <dt class="text-taupe">{{ __('vitrine.panier.livraison') }}</dt>
                                <dd class="text-taupe">{{ __('vitrine.panier.a_calculer') }}</dd>
                            </div>
                            <div class="mt-2 flex items-baseline justify-between gap-4 border-t border-filet pt-4">
                                <dt class="text-lg font-semibold text-brun">{{ __('vitrine.panier.total_estime') }}</dt>
                                <dd><x-amani.prix :montant="$sousTotal" class="text-[22px]" /></dd>
                            </div>
                        </dl>
                        <p class="mt-3 text-sm text-taupe">{{ __('vitrine.panier.livraison_aide') }}</p>

                        @if (Route::has(app()->getLocale().'.commande.livraison') && $nombre > 0)
                            <x-amani.bouton :href="route_langue('commande.livraison')" icone-apres="fleche" :pleine-largeur="true" class="mt-6">{{ __('vitrine.etapes.livraison') }}</x-amani.bouton>
                        @endif
                    </div>
                </aside>
            </div>
        @endif

        @if ($suggestions->isNotEmpty())
            <section class="mt-16">
                <x-amani.titre-section :titre="__('vitrine.panier.suggestions')" :lien="route_langue('boutique')" />
                <ul class="mt-6 grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">
                    @foreach ($suggestions as $produit)
                        <li class="flex"><x-amani.carte-produit :produit="$produit" class="w-full" /></li>
                    @endforeach
                </ul>
            </section>
        @endif
    </div>
</x-layouts.vitrine>
