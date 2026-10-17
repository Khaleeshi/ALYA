<x-filament-widgets::widget>
    <x-filament::section heading="Actions rapides">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($this->raccourcis() as $raccourci)
                <a
                    href="{{ $raccourci['url'] }}"
                    @if ($raccourci['externe']) target="_blank" rel="noopener" @endif
                    class="flex items-center gap-3 rounded-xl bg-[#f5e8d7]/70 px-4 py-3.5 text-sm font-medium text-[#493729] transition hover:bg-[#f5e8d7]"
                >
                    <span class="flex size-9 items-center justify-center rounded-full bg-white text-[#9c6b3f]">
                        <x-amani.icone :nom="$raccourci['icone']" :taille="20" />
                    </span>
                    {{ $raccourci['libelle'] }}
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
