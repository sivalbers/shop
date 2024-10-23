<div>
    <form wire:submit.prevent="update">
        <div class="border border-gray-500 w-1/3 m-5 rounded">
            <div class="grid grid-cols-3 gap-4 m-4">
                <div class="col-span-3 bg-slate-300 rounded p-3 font-bold">
                    Sortiment bearbeiten
                </div>



                <div>
                    <label for="bezeichnung">Bezeichnung</label>
                </div>

                <div>
                    <input type="text" wire:model="bezeichnung" id="bezeichnung">
                </div>

                <div>
                    @error('bezeichnung')
                        <span>{{ $message }}</span>
                    @enderror
                </div>

                <div class="text-right">
                    <button class="border border-gray-400 rounded px-2 py-1" type="button"
                        wire:click="cancel">Abbrechen</button>
                </div>

                <div>
                    <button class="text-right border border-gray-400 rounded px-2 py-1"
                        type="submit">Aktualisieren</button>
                </div>

                <div>

                </div>


            </div>
        </div>

    </form>
</div>
