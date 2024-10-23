<div>
    <div class="p-3 mb-4 flatwhite">

        <div class="text-base font-bold text-sky-600 border-b border-sky-600 h-6">
            Ihr aktueller Warenkorb</div>


        <form wire:submit.prevent="update">
            @csrf
            <div class="flex flex-col mt-4 text-sm ">
                <div class="flex flex-row mb-2 underline ">
                    <div class="basketLabel font-bold text-xl">Bestellung:</div>
                    <div class="font-bold text-xl mt-0.5">{{ $bestellung->nr }}</div>
                </div>
                <div class="flex flex-row mb-1">
                    <label class="basketLabel" for="kundenbestellnr">Kundenbestellnr.:</label>
                    <input type="text" wire:model="kundenbestellnr" id="kundenbestellnr" class="basketInput">
                </div>
                <div class="flex flex-row mb-1">
                    <label class="basketLabel" for="lieferdatum">Lieferdatum:</label>
                    <input type="date" wire:model="lieferdatum" id="lieferdatum" class="basketInput">
                </div>

                <div class="flex flex-row mb-1">
                    <label class="basketLabel" for="kommission">Kommission:</label>
                    <input type="text" wire:model="kommission" id="kommission" class="basketInput">
                </div>

                <div class="flex flex-row mb-1">
                    <label class="basketLabel" for="bemerkung">Bemerkung: (abweichende Lieferadresse bitte hier eingeben.)</label>
                    <textarea wire:model="bemerkung" id="bemerkung" class="basketTextarea"></textarea>
                </div>

                <div class="flex flex-row mb-1 mt-4 text-base">

                    <div class="basketLabel font-bold">Rechnungsadresse:</div>
                    <div class="w-8/12">{!! $rechnungsadresse !!}</div>
                </div>

                <div class="flex flex-row mb-1 mt-4 text-base">
                    <div class="basketLabel font-bold">Lieferadresse:</div>

                    <div class="w-8/12">{!! $lieferadresse !!}</div>

                </div>


            </div>

            <button type="submit"
                class="mt-2 px-4 py-2 bg-sky-600 text-white rounded-md shadow-md shadow-gray-400">Speichern</button>



        </form>
    </div>
    <div x-data="{ showEmptyButton: false }" class="p-3 flatwhite">

        <div class="text-base font-bold text-sky-600 border-b border-sky-600">Warenkorb leeren</div>

        <div class="p-4"> Mit klick auf "Leeren ..." werden nach Rückfrage alle Positionen und Kundenfelder der
            aktuellen Bestellung unwiederruflich gelöscht!</div>

        <button x-show="showEmptyButton === false" type="button" @click="showEmptyButton = true";
            class="mt-2 px-4 py-2 bg-red-600 text-white rounded-md w-auto flex flex-row items-center shadow-md shadow-gray-400"><x-fluentui-delete-28-o
                class="h-6 pr-2" />Leeren... </button>

        <div x-show="showEmptyButton" x-cloak>
            <button @click="showEmptyButton = false";
                class="mt-2 px-4 py-2 text-black bg-[#CDD503] rounded-md shadow-md shadow-gray-400">Abbrechen?</button>
            <button wire:click="doEmpty; showEmptyButton = false"
                class="mt-2 px-4 py-2 bg-red-600 text-white rounded-md shadow-md shadow-gray-400">Wirklich
                leeren?</button>
        </div>
    </div>


</div>
