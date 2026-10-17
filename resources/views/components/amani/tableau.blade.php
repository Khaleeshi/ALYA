{{--
    Tableau (espace client, espace livreur, récapitulatifs) : carte blanche,
    en-têtes sur fond crème, chiffres alignés.

    colonnes : liste de libellés, ou de ['libelle' => …, 'nombre' => true] pour
    aligner une colonne de chiffres à droite. Les lignes vont dans le slot (<tr>).
--}}
@props(['colonnes' => [], 'legende' => null])

<div {{ $attributes->class('overflow-x-auto rounded-carte border border-filet bg-blanc') }}>
    <table class="w-full min-w-[560px] border-collapse text-left text-sm [&_td]:border-t [&_td]:border-filet [&_td]:px-4 [&_td]:py-3 [&_td]:align-middle [&_td]:text-brun [&_td.nombre]:chiffres [&_td.nombre]:text-right">
        @if ($legende)
            <caption class="sr-only">{{ $legende }}</caption>
        @endif
        <thead class="bg-creme">
            <tr>
                @foreach ($colonnes as $colonne)
                    @php($colonne = is_array($colonne) ? $colonne : ['libelle' => $colonne])
                    <th scope="col" @class(['px-4 py-3 text-[13px] font-semibold text-taupe', 'text-right' => $colonne['nombre'] ?? false])>{{ $colonne['libelle'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>
