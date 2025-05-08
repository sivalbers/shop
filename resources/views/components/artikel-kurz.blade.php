<div class="flex flex-row w-full hover:bg-[#e3e692] items-center">
   @php
       $abstand = 'py-0.5';
   @endphp

    <div class="basis-4/6 px-1 {{ $abstand }}">
        <span class="text-sm flex flex-row">


            <span class="relative group text-gray-300 pr-2 hover:text-yellow-500">
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
            </span>


            <span class="relative group text-gray-500 pr-2">
                <x-fluentui-info-12-o class="w-5" />
                @if (!empty(trim($pos['langtext'])))
                    <span class="absolute hidden group-hover:block bg-gray-700 text-white text-xs rounded py-1 px-2 w-80 -mt-8 z-10">
                        {!! $pos['langtext'] !!}
                    </span>
                @endif
            </span>
            <div class="w-full">
                <a href="#" wire:click.prevent="showArtikel('{{ $pos['artikelnr'] }}')" class="hover:underline">
                    <span class="font-bold ">{{ $pos['artikelnr'] }} - </span>{{ $pos['bezeichnung'] }}
                </a>
            </div>
        </span>
    </div>
    <div class="basis-2/6 px-1 {{ $abstand }}">
        <div class="flex flex-col ml-auto w-full">
            <div class="flex flex-row items-center ml-auto">
                <div class="flex-grow-0 w-auto">
                    <div class="basis-1 text-red-800 text-sm text-right">
                        {{ formatPreis( $pos['vkpreis']) }} € / {{ $pos['einheit'] }}
                    </div>
                </div>

                <div class="flex-grow-0 w-auto ml-2" >
                    <div x-data="{ quantity: {{ $pos['menge'] }}, loop:0 }"
                        @basket-cleared.window="quantity = 0"
                        x-init="quantity = {{ $pos['menge'] }}"
                        class="flex items-center overflow-hidden w-24 py-0 border border-gray-400 rounded">

                        <button type="button" @click="quantity = Math.max(0, quantity - 1); $wire.set('aPositions.{{ $loop->index }}.menge', quantity)"
                            class="flex-1 bg-gray-200 text-gray-700 py-0.5 hover:bg-blue-200 h-7 border-r border-r-gray-400">-</button>

                        <input type="number" min="1" max="1000000" step="1" x-model="quantity" wire:model="aPositions.{{ $loop->index }}.menge"
                            class="InputMenge px-1 w-14 text-center border-none outline-none text-xs h-7"
                            @focus="$event.target.select()">

                        <button type="button" @click="quantity++; $wire.set('aPositions.{{ $loop->index }}.menge', quantity)"
                            class="flex-1 bg-gray-200 text-gray-700 py-0.5 hover:bg-blue-200 h-7 border-l border-l-gray-400">+</button>

                        <!-- input type="hidden" name="artikelmenge[{{ $pos['artikelnr'] }}]" x-model="quantity" -->
                    </div>
                </div>
                <div class="flex-grow-0  px-1">
                    <button type="submit" class="text-sky-600 pt-[4px] f" title="In den Warenkorb">
                        <x-fluentui-shopping-bag-16-o class="h-6" />
                    </button>
                </div>
                <div class="flex-grow-0 w-auto ml-2">

                    @if ($pos['bestand'] == 0)
                        <x-fluentui-vehicle-truck-profile-24-o class="h-7 text-red-500" />
                    @else
                        <x-fluentui-vehicle-truck-profile-24 class="h-7 text-[#CDD503] " />
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
