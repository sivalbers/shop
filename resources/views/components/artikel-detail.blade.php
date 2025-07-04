@props(['pos', 'loop', 'tabFavoritActive' => false, 'favoritenActiveId', ])

<div class="flex flex-col md:flex-row mt-2 border-b-2 border-sky-600 w-full ">


        <!-- Spalte 1 -->
        <div class="flex flex-col w-8/12 pr-1 ">
            <div class="flex flex-row items-center border-b border-gray-200">
                @if ($tabFavoritActive) <!-- Favoriten -->
                <div class="mr-2">
                    <a href="#"  wire:click.prevent="favoritArtikelDelete( '{{ $pos['id'] }}')"


                        class="hover:bg-[#e3e692] ">

                        <x-fluentui-delete-16-o class="h-5" />
                    </a>
                </div>
                @endif
                <div class="relative group text-gray-300 pr-1 hover:text-yellow-500">
                    <a href="#" wire:click.prevent="favoritArtikelForm('{{ $pos['artikelnr'] }}')" class="hover:underline">
                        @if ($pos['is_favorit'])
                        <x-fluentui-star-emphasis-20 class="text-yellow-500 w-5" />
                        @else
                            <!-- Normaler Zustand -->
                            <x-fluentui-star-28-o class="w-5 group-hover:hidden" />

                            <!-- Hover-Zustand -->
                            <x-fluentui-star-28-o class="w-5 hidden group-hover:block" />
                        @endif
                    </a>
                </div>

                <div class="flex flex-row justify-between w-full font-bold">
                    <div class="text-base">
                        <a href="#" wire:click.prevent="showArtikel('{{ $pos['artikelnr'] }}')" class="hover:underline">
                            {{ $pos['artikelnr'] }} - {{ $pos['bezeichnung'] }}
                        </a>
                    </div>
                    <div class="">
                        &nbsp;
                    </div>
                </div>
            </div>
            <div class="text-sm">
                {!! $pos['langtext'] !!}
            </div>
        </div>


       <!-- Spalte 2 -->


    <!-- Spalte 3 -->


    <div class="flex flex-col w-full md:w-4/12 items-end text-left  space-y-1">
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
                            class="flex-1 bg-gray-200 text-gray-700 hover:bg-blue-200 h-7 border-r border-r-gray-400">-</button>
                    <input type="number" x-model="quantity" wire:model="aPositions.{{ $loop->index }}.menge"
                        class="InputMenge w-14 text-center border-none outline-none text-xs h-7"
                        @focus="$event.target.select()">
                    <button type="button" @click="quantity++; $wire.set('aPositions.{{ $loop->index }}.menge', quantity)"
                            class="flex-1 bg-gray-200 text-gray-700 hover:bg-blue-200 h-7 border-l border-l-gray-400">+</button>
                </div>

                {{-- Icons --}}
                <button type="submit" class="text-sky-600" title="In den Warenkorb">
                    <x-fluentui-shopping-bag-20-o class="h-7" />
                </button>
                @if ($pos['bestand'] == 0)
                    <x-fluentui-vehicle-truck-profile-24-o class="h-7 text-red-500" />
                @else
                    <x-fluentui-vehicle-truck-profile-24 class="h-7 text-[#CDD503]" />
                @endif
            </div>
        </div>

        <div class="flex flex-col w-full px-2 items-end my-2">
            @php
                $bilder = imageExistsAll($pos['artikelnr']);
            @endphp

            <x-product-image :images="$bilder" size="100" artikelnr="{{ $pos['artikelnr'] }}" beschreibung="{{ $pos['bezeichnung'] }}" />
        </div>

    </div>





</div>
