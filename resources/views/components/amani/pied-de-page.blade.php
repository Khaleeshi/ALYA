{{--
    Pied de page (cahier §4.2, maquettes) : logo et signature, liens utiles,
    aide, réseaux, newsletter. Une colonne sans contenu n'est pas affichée ;
    la newsletter attend la définition du consentement et de la
    désinscription (cahier §4.2).

    liens / aide : liens existants (Navigation::principale(), Navigation::aide()).
    reseaux : [nom => adresse] des comptes officiels renseignés.
--}}
@props(['liens' => [], 'aide' => [], 'reseaux' => []])

<footer {{ $attributes->class('bg-creme') }}>
    <div class="conteneur grid gap-10 py-14 sm:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1fr_1.4fr] lg:gap-8">
        <div class="flex flex-col items-start gap-4">
            <x-amani.logo />
            <p class="max-w-56 text-[15px] leading-snug text-brun">{{ __('vitrine.signature') }}</p>
        </div>

        @if (count($liens) > 0)
            <section>
                <h2 class="mb-4 text-[15px] font-semibold text-brun">{{ __('vitrine.pied.liens_utiles') }}</h2>
                <ul class="flex flex-col gap-2 text-sm text-taupe">
                    @foreach ($liens as $lien)
                        <li><a href="{{ $lien['url'] }}" class="hover:text-brun">{{ $lien['libelle'] }}</a></li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if (count($aide) > 0)
            <section>
                <h2 class="mb-4 text-[15px] font-semibold text-brun">{{ __('vitrine.pied.aide') }}</h2>
                <ul class="flex flex-col gap-2 text-sm text-taupe">
                    @foreach ($aide as $lien)
                        <li><a href="{{ $lien['url'] }}" class="hover:text-brun">{{ $lien['libelle'] }}</a></li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if (count($reseaux) > 0)
            <section>
                <h2 class="mb-4 text-[15px] font-semibold text-brun">{{ __('vitrine.pied.nous_suivre') }}</h2>
                <ul class="flex flex-col gap-2 text-sm text-taupe">
                    @foreach ($reseaux as $nom => $url)
                        <li><a href="{{ $url }}" class="hover:text-brun" rel="noopener" target="_blank">{{ $nom }}</a></li>
                    @endforeach
                </ul>
            </section>
        @endif

        @isset($newsletter)
            <section>
                <h2 class="mb-4 text-[15px] font-semibold text-brun">{{ __('vitrine.pied.newsletter') }}</h2>
                {{ $newsletter }}
            </section>
        @endisset
    </div>

    <div class="border-t border-filet">
        <p class="conteneur py-5 text-[13px] text-taupe">{{ __('vitrine.pied.droits', ['annee' => now()->year]) }}</p>
    </div>
</footer>
