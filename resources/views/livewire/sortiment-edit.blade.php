<div>
    <form wire:submit.prevent="{{ ($updateMode) ? 'update' : 'store' }}">
        <div class="flatwhite w-1/3 mt-6 m-auto ">

                <div class="flex flex-col">
                    <div class="px-2 font-bold text-xl text-sky-600 border-b border-sky-600 py-2">

                        {{ ($updateMode) ? 'Sortiment bearbeiten' : 'Sortiment anlegen' }}
                    </div>

                    <div class="flex flex-row items-center w-full py-1 pt-4">
                        <div class="pl-2 w-1/3">
                            <label for="bezeichnung">Bezeichnung</label>
                        </div>

                        <div  class="w-2/3 pr-2">
                            <input type="text" wire:model="bezeichnung" id="bezeichnung"  class="w-full">
                            <div>
                                @error('bezeichnung')
                                    <span>{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                    </div>

                    <div class="flex flex-row items-center w-full py-1">
                        <div class="pl-2 w-1/3">
                            <label for="anzeigename">Anzeigename</label>
                        </div>

                        <div class="w-2/3 pr-2">
                            <input type="text" wire:model="anzeigename" id="anzeigename" class="w-full">
                            <div>
                                @error('anzeigename')
                                    <span>{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                    </div>
                    <div class="flex flex-row items-center w-full justify-between p-8 py-4">
                        <div class="">
                            <button class="border  rounded px-2 py-1 bg-ewe-ltgruen" type="button" wire:click="cancel">Abbrechen</button>
                        </div>

                        <div>
                            <button class="border border-gray-400 rounded px-2 py-1 bg-sky-600 text-white" type="submit">Aktualisieren</button>
                        </div>
                    </div>
                </div>

        </div>

    </form>
</div>
