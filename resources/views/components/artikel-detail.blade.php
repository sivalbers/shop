@props(['pos', 'loop', 'tabFavoritActive' => false, 'favoritenActiveId', ])

<div class="flex flex-col md:flex-row mt-2 border-b-2 border-sky-600 w-full ">


        <!-- Spalte 1 -->
<div class="w-full md:w-8/12 pr-1 ">

    {{-- Bild rechts oben, floatend --}}


    {{-- Titelzeile (fließt links vom Bild) --}}
    <div class="font-bold text-base mb-1">
        @if ($tabFavoritActive)
            <a href="#" wire:click.prevent="favoritArtikelDelete('{{ $pos['id'] }}')" class="hover:bg-[#e3e692] inline-block mr-1 align-middle">
                <x-fluentui-delete-16-o class="h-5" />
            </a>
        @endif

        <a href="#" wire:click.prevent="favoritArtikelForm('{{ $pos['artikelnr'] }}')" class="hover:underline inline-block mr-1 align-middle">
            @if ($pos['is_favorit'])
                <x-fluentui-star-emphasis-20 class="text-yellow-500 w-5" />
            @else
                <x-fluentui-star-28-o class="w-5" />
            @endif
        </a>

        <a href="#" wire:click.prevent="showArtikel('{{ $pos['artikelnr'] }}')" class="hover:underline align-middle">
            {{ $pos['artikelnr'] }} - {{ $pos['bezeichnung'] }}
        </a>
    </div>

    {{-- Langtext --}}
    <div class="text-sm">

        <div class="float-right ml-2 mb-1 ">
            @php
                $bilder = imageExistsAll($pos['artikelnr']);
            @endphp
            <x-product-image :images="$bilder" size="130" artikelnr="{{ $pos['artikelnr'] }}" beschreibung="{{ $pos['bezeichnung'] }}" />
        </div>
        {!! $pos['langtext'] !!}

    </div>

    {{-- Float beenden --}}
    <div class="clear-both"></div>
</div>







       <!-- Spalte 2 -->


    <!-- Spalte 3 -->


    <div class="flex flex-col w-full md:w-4/12 items-end text-left  space-y-1 ">
        {{-- Zeile: Preis + Menge + Icons in einer Zeile, zentriert --}}
        <div class="flex basis-1 flex-row md:flex-col   space-x-2">
            <div class="text-red-800 text-lg  text-left">
                {{ formatPreis($pos['vkpreis']) }} € / {{ $pos['einheit'] }}
            </div>
            <div class="flex felx-row items-center">
                {{-- Menge --}}
                <div x-data="{ quantity: {{ $pos['menge'] }} }"
                    @basket-cleared.window="quantity = 0"
                    x-init="quantity = {{ $pos['menge'] }}"
                    class="flex items-center overflow-hidden w-24 border border-gray-400 rounded">
                    <button type="button" @click="quantity = Math.max(0, quantity - 1); $wire.set('aPositions.{{ $loop->index }}.menge', quantity)"
                            class="cursor-pointer flex-1 bg-gray-200 text-gray-700 hover:bg-blue-200 h-7 border-r border-r-gray-400">-</button>
                    <input type="number" x-model="quantity" wire:model="aPositions.{{ $loop->index }}.menge"
                        class="InputMenge w-14 text-center border-none outline-none text-xs h-7"
                        @focus="$event.target.select()">
                    <button type="button" @click="quantity++; $wire.set('aPositions.{{ $loop->index }}.menge', quantity)"
                            class="cursor-pointer flex-1 bg-gray-200 text-gray-700 hover:bg-blue-200 h-7 border-l border-l-gray-400">+</button>
                </div>

                {{-- Icons --}}
                <button type="submit" class="cursor-pointer text-sky-600" title="In den Warenkorb">
                    <x-fluentui-shopping-bag-20-o class="h-7" title="In den Warenkorb"/>
                </button>
                @if ($pos['bestand'] == 0)
                    <x-fluentui-vehicle-truck-profile-24-o class="h-7 text-red-500" title="Artikel nicht auf Lager" />
                @else
                    <x-fluentui-vehicle-truck-profile-24 class="h-7 text-[#CDD503]" title="Artikel auf Lager"/>
                @endif
            </div>
        </div>



    </div>





</div>
