{{--
    Tableau dense (administration hors Filament, espace livreur, récapitulatifs) :
    lignes serrées, chiffres tabulaires, filets de 1 px, aucune tuile décorative.

    colonnes : liste de libellés, ou de ['libelle' => …, 'nombre' => true] pour
    aligner une colonne de chiffres à droite. Les lignes vont dans le slot (<tr>).
--}}
@props(['colonnes' => [], 'legende' => null])

<div {{ $attributes->class('overflow-x-auto border-t border-filet') }}>
    <table class="w-full min-w-[560px] border-collapse text-left text-sm [&_td]:border-b [&_td]:border-filet [&_td]:py-3 [&_td]:pr-4 [&_td]:align-top [&_td]:text-ivoire [&_td.nombre]:chiffres [&_td.nombre]:text-right [&_td.nombre]:pr-0">
        @if ($legende)
            <caption class="sr-only">{{ $legende }}</caption>
        @endif
        <thead>
            <tr>
                @foreach ($colonnes as $colonne)
                    @php($colonne = is_array($colonne) ? $colonne : ['libelle' => $colonne])
                    <th scope="col" @class(['etiquette border-b border-filet py-3 pr-4 align-bottom font-medium text-gris', 'text-right pr-0' => $colonne['nombre'] ?? false])>{{ $colonne['libelle'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>
