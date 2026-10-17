{{-- Encadré d'information (maquette 5 : « Les créneaux peuvent varier… »). --}}
@props(['icone' => 'info'])

<div {{ $attributes->class('flex gap-3 rounded-champ bg-nude-pale px-4 py-3 text-sm text-taupe') }}>
    <x-amani.icone :nom="$icone" :taille="20" class="mt-px text-bronze" />
    <div>{{ $slot }}</div>
</div>
