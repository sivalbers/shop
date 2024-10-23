<div class="flex flex-row mb-4 border-b-2 border-sky-600 w-full">
    <div class="relative basis-3/4 p-2">
        <div class="absolute top-2 right-2 float-left">
            @if ($artikel->artikelnr=='650000')
            <x-product-image image="650000.png" size="100" class="" />
            @else
            <x-product-image image="blank.png" size="100" class="" />
            @endif
        </div>

        <span class="text-lg">
            <a href="#" wire:click.prevent="showArtikel('{{ $artikel->artikelnr }}')" class="hover:underline">
                {{ $artikel->bezeichnung }}
            </a>
        </span><br>
        <span class="text-base">Artikelnr: {{ $artikel->artikelnr }}</span><br>
        <div class="text-xs max-h-15 leading-tight overflow-hidden">{!! $artikel->langtext !!}</div>
    </div>
    <div class="basis-1/4 p-2">
        <div class="flex flex-col items-end">
            <div class="basis-1 text-red-800 text-lg">
                {{ number_format($artikel->vkpreis, 2, ',', '.') }} € / {{ $artikel->einheit }}
            </div>
            <br>
            <div class="flex flex-row">

                <div x-data="{ quantity: {{ $quantities[$artikel->artikelnr]['menge'] }} }"
                    @basket-cleared.window="quantity = 0"
                    x-init="quantity = {{ $quantities[$artikel->artikelnr]['menge'] }}"
                    class="flex items-center overflow-hidden w-24 py-0 border border-gray-400 rounded">
                    <button type="button" @click="quantity = Math.max(0, quantity - 1); $wire.set('quantities.{{ $artikel->artikelnr }}.menge', quantity)"
                        class="flex-1 bg-gray-200 text-gray-700 py-0.5 hover:bg-blue-200 h-full border-r border-r-gray-400">-</button>
                    <input type="text" x-model="quantity" wire:model="quantities.{{ $artikel->artikelnr }}.menge"
                        class="w-14 text-center border-none outline-none text-xs" >
                    <button type="button" @click="quantity++; $wire.set('quantities.{{ $artikel->artikelnr }}.menge', quantity)"
                        class="flex-1 bg-gray-200 text-gray-700 py-0.5 hover:bg-blue-200 h-full border-l border-l-gray-400">+</button>
                    <input type="hidden" name="artikelmenge[{{ $artikel->artikelnr }}]" x-model="quantity">
                </div>
                <div class="basis-1 text-xs ml-2">

                    @if ($quantities[$artikel->artikelnr]['bestand'] === 0)
                        <x-fluentui-vehicle-truck-profile-24-o class="h-7 text-red-500" />
                    @else
                        <x-fluentui-vehicle-truck-profile-24 class="h-7 text-[#CDD503]" />
                    @endif

            </div>

            </div>
        </div>
    </div>
</div>
