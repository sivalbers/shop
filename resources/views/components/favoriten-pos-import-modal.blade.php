<div
    class="flex fixed top-0 bg-opacity-60 items-center justify-center w-full h-full bg-slate-100 backdrop-blur-[2px] "
    x-data="importModal(@entangle('showFavoritenPosImportModal'))"
    x-show="offen"
    x-cloak
    x-on:click.self="offen = false"
    x-on:keydown.escape.window="offen = false"
>

    <div class="w-5/12 m-auto flatwhite ">
        <form wire:submit.prevent="favoritenPosImport" enctype="multipart/form-data">
            <div class="flex flex-col space-y-4 p-4">
                <div class="font-bold text-xl border-b-2 border-b-[#CDD503] pb-2">
                    Favoriten-Positionen importieren
                </div>

                <!-- Dropzone -->
                <div
                    x-on:dragover.prevent="dragging = true"
                    x-on:dragleave.prevent="dragging = false"
                    x-on:drop.prevent="drop($event)"
                    :class="dragging ? 'border-yellow-400 bg-yellow-50' : 'border-gray-300'"
                    class="border-2 border-dashed rounded-lg p-6 text-center cursor-pointer transition-colors duration-200"
                    onclick="document.getElementById('importFile').click()"
                >
                    <template x-if="fileName">
                        <p class="text-gray-800 font-semibold">📄 Datei: <span x-text="fileName"></span></p>
                    </template>

                    <template x-if="!fileName">
                        <p class="text-gray-500">📂 Datei hier ablegen oder klicken, um eine CSV auszuwählen</p>
                    </template>

                    <input
                        type="file"
                        wire:model="importFile"
                        id="importFile"
                        class="hidden"
                        x-on:change="fileName = $event.target.files.length ? $event.target.files[0].name : ''"
                    >
                </div>

                @error('importFile')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror

                <div wire:loading wire:target="importFile" class="text-blue-500">
                    Datei wird geladen...
                </div>

                <!-- Checkbox -->
                <div class="flex items-center space-x-2">
                    <label class="w-2/6">Vor Import leeren:</label>
                    <input type="checkbox" wire:model="importOverride" class="text-sm">
                </div>

                <!-- Button -->
                <div class="flex justify-end">
                    <button type="submit" class="py-2 px-4 border border-gray-400 bg-[#CDD503] text-black rounded-md hover:bg-[#e3e692]">
                        Import starten
                    </button>
                </div>
            </div>
        </form>
    </div>

<script>
    function importModal(offen) {
        return {
            offen,
            dragging: false,
            fileName: '',
            drop(e) {
                this.dragging = false;
                document.getElementById('importFile').files = e.dataTransfer.files;
                document.getElementById('importFile').dispatchEvent(new Event('change'));
            },
            init() {
                this.$watch('offen', value => {
                    if (value === true) {
                        this.fileName = '';
                        document.getElementById('importFile').value = ''; // sicherheitshalber
                    }
                });
            }
        }
    }
</script>

</div>
