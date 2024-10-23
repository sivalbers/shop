<div>
    <div class="w-full text-xs">
        <div class="w-full m-auto">
            <!-- Grid Header -->
            <div class="grid grid-cols-6 gap-1 m-4">
                <div class="bg-slate-300 p-2 font-bold text-center">Lfd.-Nr.</div>
                <div class="bg-slate-300 p-2 font-bold text-center">Artikelnr.</div>
                <div class="bg-slate-300 p-2 font-bold text-right">Menge</div>
                <div class="bg-slate-300 p-2 font-bold text-right">E-Preis</div>
                <div class="bg-slate-300 p-2 font-bold text-right">G-Preis</div>
                <div class="bg-slate-300 p-2 font-bold text-right">Steuer</div>
            </div>

            <!-- Grid Rows -->
            @if (!empty($positionen))
                <div class="grid grid-cols-6 gap-1 m-4">
                    @php
                        $lfdNr = 0;
                    @endphp
                    @foreach ($positionen as $position)
                        @php
                            $lfdNr++;
                        @endphp
                        <div class=" text-right">{{ $lfdNr }}</div>
                        <div class=" text-center">{{ $position->artikelnr }}</div>
                        <div class=" text-right">{{ number_format($position->menge, 0, ',', '.') }}</div>
                        <div class=" text-right">{{ number_format($position->epreis, 2, ',', '.') }} €</div>
                        <div class=" text-right">{{ number_format($position->gpreis, 2, ',', '.') }} €</div>
                        <div class=" text-right">{{ $position->steuer }}</div>
                    @endforeach
                </div>
            @else
                <div class="m-4 text-center">
                    Keine Positionen verfügbar.
                </div>
            @endif
        </div>
    </div>
</div>
