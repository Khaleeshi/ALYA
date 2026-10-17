{{--
    Accueil (cahier §5.1, maquette 1) : bannière de marque avec la signature,
    bande de réassurance, catégories, nouveautés, collections, coffrets.
    Une section vide ne s'affiche pas. La photo de la bannière et le bandeau
    promotionnel viendront avec les contenus et les promotions.
--}}
@use('App\Models\Categorie')
@use('App\Models\Gamme')
@use('App\Models\Produit')

<x-layouts.vitrine :description="__('vitrine.accueil_page.accroche')" :reassurance="false">
    <section class="relative isolate overflow-hidden bg-gradient-to-br from-creme via-nude-pale to-nude">
        <div class="conteneur flex min-h-[52dvh] flex-col items-center justify-center gap-5 py-16 text-center lg:py-24">
            <p class="sur-titre text-taupe">{{ __('vitrine.accueil_page.sur_titre') }}</p>
            <h1 class="titre text-[44px] leading-[1.05] text-brun sm:text-6xl lg:text-7xl">
                {{ __('vitrine.signature_debut') }}
                <span class="manuscrit mt-2 block text-[64px] text-bronze sm:text-[88px] lg:text-[104px]">{{ __('vitrine.signature_fin') }}</span>
            </h1>
            <p class="max-w-xl text-lg text-brun/85">{{ __('vitrine.accueil_page.accroche') }}</p>
            @if (Route::has(app()->getLocale().'.collections'))
                <x-amani.bouton :href="route_langue('collections')" icone-apres="fleche" class="mt-2">{{ __('vitrine.accueil_page.bouton') }}</x-amani.bouton>
            @endif
        </div>
    </section>

    <x-amani.bande-reassurance />

    <div class="conteneur flex flex-col gap-14 py-12 lg:gap-16 lg:py-16">
        @if ($categories->isNotEmpty())
            <section>
                <h2 class="sr-only">{{ __('vitrine.accueil_page.categories') }}</h2>
                <div class="grid gap-5 md:grid-cols-3">
                    @foreach ($categories as $categorie)
                        <x-amani.carte-categorie
                            :titre="$categorie->nom"
                            :texte="$categorie->description"
                            :url="route_langue('boutique.categorie', ['categorie' => $categorie->slug])"
                            :photo="$categorie->getFirstMedia(Categorie::COUVERTURE)"
                        />
                    @endforeach
                </div>
            </section>
        @endif

        @if ($nouveautes->isNotEmpty())
            <section>
                <x-amani.titre-section :titre="__('vitrine.accueil_page.nouveautes')" :lien="route_langue('boutique')" />
                <ul class="mt-6 grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">
                    @foreach ($nouveautes as $produit)
                        <li class="flex"><x-amani.carte-produit :produit="$produit" class="w-full" /></li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if ($gammes->isNotEmpty())
            <section>
                <x-amani.titre-section :titre="__('vitrine.accueil_page.collections')" :lien="route_langue('collections')" />
                <ul class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($gammes as $gamme)
                        <li class="flex">
                            <x-amani.carte-collection
                                :titre="$gamme->nom"
                                :texte="$gamme->accroche"
                                :url="route_langue('collections.gamme', ['gamme' => $gamme->slug])"
                                :photo="$gamme->getFirstMedia(Gamme::COUVERTURE)"
                                class="w-full"
                            />
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if ($coffrets->isNotEmpty())
            <section>
                <x-amani.titre-section :titre="__('vitrine.accueil_page.coffrets')" :lien="route_langue('coffrets')" />
                <ul class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($coffrets as $coffret)
                        <li class="flex">
                            <x-amani.carte-collection
                                :titre="$coffret->nom"
                                :texte="$coffret->accroche"
                                :url="route_langue('boutique.produit', ['produit' => $coffret->slug])"
                                :photo="$coffret->getFirstMedia(Produit::PHOTOS)"
                                :action="__('vitrine.coffrets.voir')"
                                class="w-full"
                            />
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if ($nouveautes->isEmpty())
            <p class="text-center text-lg text-taupe">{{ __('vitrine.accueil_page.texte') }}</p>
        @endif
    </div>
</x-layouts.vitrine>
