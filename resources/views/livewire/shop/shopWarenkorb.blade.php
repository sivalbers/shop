<div>
    <div class="p-3 mb-2 flatwhite">

        <div
            class="text-base font-bold text-sky-600 border-b border-sky-600 h-6
         ">
            Ihr aktueller Warenkorb</div>

        @php
            $flexclass = 'flex-row md:flex-col lg:flex-row lg:items-center';
            $fldClass = 'md:text-right md:justify-right lg:text-left lg:justify-left';
        @endphp

        <form wire:submit.prevent="updateWarenkorb" class="">
            @csrf
            <div class="flex flex-col mt-4 text-sm ">

                <div class="flex {{ $flexclass }} mb-2 underline w-full">
                    <div class="w-1/3 text-right font-bold text-xl mr-2">
                        Bestellung:
                    </div>
                    <div class="w-2/3 font-bold text-xl mt-0.5 {{ $fldClass }}">
                        {{ $bestellung->nr }}
                    </div>
                </div>

                <div class="flex {{ $flexclass }} mb-1 ">
                    <div class="w-1/3 text-right md:text-left lg:text-right mr-2">
                        <label for="kundenbestellnr">Kundenbestellnr.:</label>
                    </div>
                    <div class="w-2/3 md:w-full lg:w-2/3">
                        <input type="text" wire:model="kundenbestellnr" id="kundenbestellnr"
                            class="basketInput {{ $fldClass }} w-full">
                    </div>
                </div>

                <div class="flex  {{ $flexclass }} mb-1">
                    <div class="w-1/3 text-right  md:text-left lg:text-right mr-2">
                        <label for="kommission">Kommission:</label>
                    </div>
                    <div class="w-2/3 md:w-full lg:w-2/3">
                        <input type="text" wire:model="kommission" id="kommission"
                            class="basketInput {{ $fldClass }}  w-full">
                    </div>
                </div>

                <div class="flex {{ $flexclass }} mb-1">
                    <div class="mr-2 w-1/3 md:mr-0 md:w-full lg:mr-2 lg:w-1/3 text-right  md:text-left lg:text-right">
                        <label for="bemerkung">
                            Bemerkung:<br> <span class="text-gray-500">(abweichende Lieferadresse bitte hier eingeben.)</span>
                        </label>
                    </div>
                    <div class="w-2/3 md:w-full lg:w-2/3">

                        <textarea wire:model="bemerkung" id="bemerkung" class="basketTextarea {{ $fldClass }}  w-full"></textarea>
                    </div>
                </div>

                <div class="flex {{ $flexclass }} mb-1">
                    <div class="w-1/3 text-right md:text-left lg:text-right  mr-2">
                        <label for="lieferdatum">Lieferdatum:</label>
                    </div>
                    <div class="w-2/3 md:w-full lg:w-2/3">
                        <input type="date" wire:model.live="lieferdatum" min="{{ $minLieferdatum }}" id="lieferdatum"
                            class="basketInput {{ $fldClass }} w-full">
                        @if ($lieferdatumError != '')
                            <div class="text-red-500 text-left">{{ $lieferdatumError }}</div>
                        @endif
                    </div>
                </div>

                <div class="flex flex-row md:flex-col xl:flex-row mb-1 mt-4 text-base">
                    <div class="w-1/3 font-bold mr-2 text-right  md:text-left lg:text-right">
                        Rechnungsadresse:
                    </div>

                    <div class="w-2/3 md:w-full lg:w-2/3">
                        {!! $rechnungsadresse !!}
                    </div>
                </div>

                <div class="flex flex-row md:flex-col xl:flex-row mb-1 mt-4 text-base">
                    <div class="w-1/3 font-bold mr-2 text-right  md:text-left lg:text-right">
                        Lieferadresse:
                    </div>

                    <div class="w-2/3 md:w-full lg:w-2/3">
                        {!! $lieferadresse !!}
                    </div>

                </div>


            </div>
            <div class="flex flex-row justify-end">
                <div>
                    <button type="submit" class="flex mt-2 px-4 py-2 bg-sky-600 text-white rounded-md shadow-md shadow-gray-400 justify-right">
                        Speichern
                    </button>
                </div>
            </div>
        </form>
    </div>



    <div x-data="{ showEmptyButton: false }" class="p-3 flatwhite">

        <div class="text-base font-bold text-sky-600 border-b border-sky-600">Warenkorb leeren</div>

        <div class="p-4"> Mit klick auf "Leeren ..." werden nach Rückfrage alle Positionen und Kundenfelder der
            aktuellen Bestellung unwiederruflich gelöscht!</div>

        <div class="flex justify-end">
            <button x-show="showEmptyButton === false" type="button" @click="showEmptyButton = true";
                class="flex flex-row mt-2 px-4 py-2 bg-red-600 text-white rounded-md w-auto  items-center shadow-md shadow-gray-400">
                    <x-fluentui-delete-28-o class="h-6 pr-2" />
                    Leeren ...
            </button>

            <div x-show="showEmptyButton" x-cloak>
                <button wire:click="doEmpty; showEmptyButton = false"
                    class="mt-2 px-4 py-2 bg-red-600 text-white rounded-md shadow-md shadow-gray-400">Wirklich
                    leeren?</button>
                <button @click="showEmptyButton = false";
                    class="mt-2 px-4 py-2 text-black bg-[#CDD503] rounded-md shadow-md shadow-gray-400">Abbrechen?</button>

            </div>
        </div>
    </div>


</div>
