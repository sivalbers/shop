<div class="flatwhite p-2 mb-2 ml-0 lg:ml-2 h-[calc(100vh-355px)] overflow-hidden">

    @php
        $s1 = 7;
        $s3 = 7;
        $s4 = 7;
        $s5 = 10;
        $s6 = 10;
        $s2 = 100-$s1-$s3-$s4-$s5-$s6
    @endphp
    <!-- Grid Header -->
    <form wire:submit.prevent="markierteBestellen">
    <div class="font-bold text-xl text-sky-600">
        <div class="flex flex-row items-center justify-between">
            <div>Bestellte Artikel</div>
            <div class="font-normal text-sm">
                <button type="submit" {{ count ($markiertePositionen) === 0 ? 'disabled' : '' }}
                    class=" border border-[#ced5037a]  rounded-md px-2 shadow-md
                     {{ count ($markiertePositionen) === 0 ? 'bg-gray-300 text-white' : 'bg-sky-600 text-white  ' }}
                     ">

                    Markierte bestellen
                </button>
            </div>

        </div>
    </div>
    <div class="flex flex-row text-sky-600 border-b border-sky-600 font-bold w-full py-0.5">
        <div class="text-left  w-[13vH] pl-2">

            <input type="checkbox" wire:model="markiereAlle" wire:click="toggleAlleCheckboxen" class="border-sky-600 ring-sky-600 focus:outline-1 focus:outline-white">
            &nbsp;Artikelnr.
        </div>
        <div class="text-left  w-[47vH]">Bezeichnung</div>
        <div class="text-right w-[7vH] pr-1">Menge</div>
        <div class="text-left  w-[5vH]">Einh.</div>
        <div class="text-right w-[13vH]">E-Preis</div>
        <div class="text-right w-[15vH] pr-2">G-Preis</div>
    </div>

    <!-- Grid Rows -->

    @if (!empty($positionen))
        <div class="overflow-y-auto max-h-[calc(100vh-426px)]">
            <div class="flex flex-col">
                @foreach ($positionen as $position)
                    <div class="flex flex-row py-0.5">
                        <div class=" text-left  w-[13vH] pl-2">
                            <input type="checkbox" wire:model.live="markiertePositionen" value="{{ $position->id }}">
                            &nbsp; {{ $position->artikelnr }}
                        </div>
                        <div class=" text-left  w-[47vH] truncate">{{ (!empty($position->artikel->bezeichnung)) ? $position->artikel->bezeichnung : 'n.v.' }}</div>
                        <div class=" text-right w-[7vH] pr-1">{{ number_format($position->menge, 0, ',', '.') }}</div>
                        <div class=" text-left  w-[5vH]">{{ (!empty($position->artikel->einheit)) ? $position->artikel->einheit : 'n.v.' }}</div>
                        <div class=" text-right w-[13vH]">{{ number_format($position->epreis, 2, ',', '.') }} €</div>
                        <div class=" text-right w-[15vH] pr-2">{{ number_format($position->gpreis, 2, ',', '.') }} €</div>

                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="m-4 text-center">
            Keine Positionen verfügbar.
        </div>
    @endif
    </form>
</div>
