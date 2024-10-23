<div class="flex flex-row mb-1 w-full">
    <div class="relative basis-3/6 p-2 border-b border-b-sky-600">
        <span class="text-sm flex flex-row">
            <span class="relative group text-gray-500 pr-2">
                <x-fluentui-info-12-o class="w-5" />
                @if (!empty(trim($artikel->langtext)))
                    <span
                        class="absolute hidden group-hover:block bg-gray-700 text-white text-xs rounded py-1 px-2 w-64 -mt-8 z-10">
                        {!! $artikel->langtext !!}
                    </span>
                @endif
            </span>
            <a href="#" wire:click.prevent="showArtikel('{{ $artikel->artikelnr }}')" class="hover:underline">


                <span class="font-bold">{{ $artikel->artikelnr }} - </span>{{ $artikel->bezeichnung }}
            </a>
        </span>
    </div>
    <div class="basis-3/6 px-2 border-b border-b-sky-600">
        <div class="flex flex-col ml-auto w-full">
            <div class="flex flex-row items-center ml-auto">
                <div class="flex-grow-0 w-auto">
                    <div class="basis-1 text-red-800 text-sm text-right">
                        {{ number_format($artikel->vkpreis, 2, ',', '.') }} € / {{ $artikel->einheit }}
                    </div>
                </div>
                <div class="flex-grow-0 w-auto ml-2">
                    <div x-data="{ quantity: {{ $quantities[$artikel->artikelnr]['menge'] }} }"
                        @basket-cleared.window="quantity = 0"
                        x-init="quantity = {{ $quantities[$artikel->artikelnr]['menge'] }}"
                        class="flex items-center overflow-hidden w-24 py-0 border border-gray-400 rounded">
                        <button type="button" @click="quantity = Math.max(0, quantity - 1); $wire.set('quantities.{{ $artikel->artikelnr }}.menge', quantity)"
                            class="flex-1 bg-gray-200 text-gray-700 py-0.5 hover:bg-blue-200 h-7 border-r border-r-gray-400">-</button>
                        <input type="text" x-model="quantity" wire:model="quantities.{{ $artikel->artikelnr }}.menge"
                            class="px-1 w-14 text-center border-none outline-none text-xs h-7">
                        <button type="button" @click="quantity++; $wire.set('quantities.{{ $artikel->artikelnr }}.menge', quantity)"
                            class="flex-1 bg-gray-200 text-gray-700 py-0.5 hover:bg-blue-200 h-7 border-l border-l-gray-400">+</button>
                        <input type="hidden" name="artikelmenge[{{ $artikel->artikelnr }}]" x-model="quantity">
                    </div>
                </div>
                <div class="flex-grow-0 w-auto ml-2">
                    @if ($quantities[$artikel->artikelnr]['bestand'] === 0)
                        <x-fluentui-vehicle-truck-profile-24-o class="h-7 text-red-500" />
                    @else
                        <x-fluentui-vehicle-truck-profile-24 class="h-7 text-[#CDD503] " />
                    @endif
                </div>
            </div>
        </div>


        
    </div>
</div>
