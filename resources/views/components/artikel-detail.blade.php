<div class="flex flex-row mt-2 border-b-2 border-sky-600 w-full ">

    <!-- Spalte 1 -->
    <div class="flex flex-col w-8/12 pr-1">
        <div class="flex flex-row items-center border-b border-gray-200">
            <div class="relative group text-gray-300 pr-1 hover:text-yellow-500">
                <a href="#" wire:click.prevent="showFavoritPosForm('{{ $pos['artikelnr'] }}')" class="hover:underline">
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
    <div class="flex flex-col w-2/12 px-2">
        @php
            $bilder = imageExistsAll($pos['artikelnr']);
        @endphp

        <x-product-image :images="$bilder" size="100" artikelnr="{{ $pos['artikelnr'] }}" beschreibung="{{ $pos['bezeichnung'] }}" />
    </div>


    <!-- Spalte 3 -->
    <div class="flex flex-col w-2/12 items-end">
        <div class="basis-1 text-red-800 text-lg">
            {{ formatPreis( $pos['vkpreis']) }} € / {{ $pos['einheit'] }}
        </div>
        <br>
        <div class="flex flex-row">

            <div x-data="{ quantity: {{ $pos['menge'] }}, loop:0 }"
                @basket-cleared.window="quantity = 0"
                x-init="quantity = {{ $pos['menge'] }}"
                class="flex items-center overflow-hidden w-24 py-0 border border-gray-400 rounded">

                <button type="button" @click="quantity = Math.max(0, quantity - 1); $wire.set('aPositions.{{ $loop->index }}.menge', quantity)"
                    class="flex-1 bg-gray-200 text-gray-700 py-0.5 hover:bg-blue-200 h-full border-r border-r-gray-400">-</button>

                <input type="text" x-model="quantity" wire:model="aPositions.{{ $loop->index }}.menge"
                    class="w-14 text-center border-none outline-none text-xs"
                    @focus="$event.target.select()">

                <button type="button" @click="quantity++; $wire.set('aPositions.{{ $loop->index }}.menge', quantity)"
                    class="flex-1 bg-gray-200 text-gray-700 py-0.5 hover:bg-blue-200 h-full border-l border-l-gray-400">+</button>

                <input type="hidden" name="artikelmenge[{{ $pos['artikelnr'] }}]" x-model="quantity">
            </div>


            <div class="basis-1 text-xs ml-2">

                @if ($pos['bestand'] == 0)
                    <x-fluentui-vehicle-truck-profile-24-o class="h-7 text-red-500" />
                @else
                    <x-fluentui-vehicle-truck-profile-24 class="h-7 text-[#CDD503]" />
                @endif

            </div>

        </div>
    </div>


</div>
