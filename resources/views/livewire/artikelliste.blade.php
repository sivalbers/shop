<div x-data="{
        showForm: @entangle('showForm'),
    }" x-on:click.self="showForm = false"
        x-on:keydown.escape.window="showForm = false">

    <div class="w-full text-xs" >
        <div class="flex flex-row w-full">
            <div class="w-1/12">
            </div>
            <div class="border border-gray-400 w-10/12 rounded m-auto">

                <div class="flex flex-col w-full ">
                    <div
                        class="flex flex-row px-2 space-x-2 bg-slate-300 border-b border-slate-600 justify-center text-lg">
                        <div class="w-2/12  rounded p-1 font-bold">
                            Artikelnr.
                        </div>
                        <div class="w-4/12 p-1 font-bold">
                            Bezeichnung
                        </div>
                        <div class="w-1/12 p-1 font-bold">
                            Status
                        </div>
                        <div class="w-1/12 text-right p-1 font-bold">
                            Preis
                        </div>
                        <div class="w-1/12  p-1 font-bold">
                            Einheit
                        </div>
                        <div class="w-1/12  p-1 font-bold">
                            WG<br>
                        </div>
                        <div class="w-1/12 p-1 font-bold">
                            Info
                        </div>
                        <div class="w-1/12 p-1 font-bold">
                            Bild
                        </div>
                    </div>

                    <div class="flex flex-row px-2 space-x-2 bg-slate-300 border-b border-slate-600">
                        <div class="w-2/12  p-1 font-bold">
                            <input type="text" wire:model.lazy="artFilter" class="suchFilter w-full"
                                placeholder="(Suche)">
                        </div>
                        <div class="w-4/12 p-1 font-bold">
                            <input type="text" wire:model.lazy="bezFilter" class="suchFilter" placeholder="(Suche)">
                        </div>
                        <div class="w-1/12 p-1 font-bold ">
                            <select id="statusFilter" wire:model.lazy="statusFilter" class="suchFilter ">
                                <option value="">Alle</option>
                                <option value="0">Aktiv</option>
                                <option value="1">Gesperrt</option>
                            </select>
                        </div>
                        <div class="w-1/12 text-right p-1 font-bold">

                        </div>
                        <div class="w-1/12 p-1 font-bold">

                        </div>
                        <div class="w-1/12 p-1">
                            <input type="text" wire:model.lazy="wgFilter" class="w-full suchFilter"
                                placeholder="(Suche)">
                        </div>
                        <div class="w-1/12 p-1">
                        </div>
                        <div class="w-1/12 p-1">
                            vorhanden
                        </div>
                    </div>



                    @foreach ($artikels as $artikel)
                        <div
                            class="flex flex-row px-2 space-x-2 hover:bg-ewe-ltgruen text-base border-b border-dotted border-gray-300">
                            <div class="w-2/12 @if ($artikel->gesperrt) line-through @endif">
                                {{ $artikel->artikelnr }}</div>
                            <div class="w-4/12 @if ($artikel->gesperrt) line-through @endif">
                                {{ $artikel->bezeichnung }}


                            </div>
                            <div class="w-1/12">{{ $artikel->gesperrt ? 'gesperrt' : '' }}</div>
                            <div class="w-1/12 text-right">{{ number_format($artikel->vkpreis, 2, ',', '.') }} €</div>
                            <div class="w-1/12">{{ $artikel->einheit }}</div>
                            <div class="w-1/12">
                                <p class="relative group">
                                    {{ $artikel->wgnr }}
                                    <span
                                        class="absolute hidden group-hover:block bg-gray-700 text-white text-xs rounded py-1 px-2 w-64 -mt-8 z-10">
                                        {!! $artikel->warengruppe->bezeichnung !!}
                                    </span>
                                </p>
                            </div>
                            <div class="w-1/12 px-1">
                                <p class="relative group">

                                    @if (!empty(trim($artikel->langtext)))
                                        Info
                                        <span
                                            class="absolute hidden group-hover:block bg-gray-700 text-white text-xs rounded py-1 px-2 w-64 -mt-8 z-10">
                                            {!! $artikel->langtext !!}
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div class="w-1/12 px-1">
                                {{ imageExistsSmall($artikel->artikelnr) !== '' ? 'Ja' : '' }}
                            </div>

                        </div>
                    @endforeach
                </div>
                <div class="col-span-7">
                    {{ $artikels->links() }}<br>

                </div>

            </div>


            <div class="w-1/12">
                <div class="ml-2 flex flex-col space-y-2">
                    <div class="border rounded bg-ewe-ltgruen p-2">
                        Bilder importieren:
                        <button class="cursor-pointer" wire:click="showUploadForm"><x-fluentui-folder-add-16-o class="w-16" /></button>
                    </div>
                    <div class="border rounded bg-ewe-ltgruen p-2">
                        Ordner importieren:
                        <button class="cursor-pointer" wire:click="importFolder" title="Es wird direkt ohne Rückfrage der 'original' Ordner importiert."><x-fluentui-folder-arrow-right-16-o class="w-16" /></button>

                        <div wire:loading wire:target="importFolder">
                            <div class="font-semibold">Bitte warten, Bilder werden verarbeitet…</div>
                        </div>


                    </div>
                </div>

            </div>
            <div class="w-1/12">
            </div>
        </div>
    </div>


    <x-my-form :isModified="$isModified"
        class="w-[97vh] sd:w-[90vh] md:w-[70vh] min-h-[30vh] max-h-[70vh] sd:max-h-[90vh] sm:max-h-[70vh] z-40 overflow-y-auto">


        <form wire:submit.prevent="saveImages" class="space-y-4">
            @csrf
            <div>
                <label for="images" class="block font-semibold">Artikelbilder hochladen</label>
                <input type="file" id="images" wire:model="images" multiple accept=".jpg,.jpeg,.png"
                    class="border p-2 w-full">

                @error('images.*')
                    <div class="text-red-500 text-sm">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex flex-wrap gap-2 mt-4">
                {{-- Vorschau der ausgewählten Dateien --}}
                @if ($images)
                    @foreach ($images as $image)
                        <div class="w-24 h-24 border rounded overflow-hidden">
                            <img src="{{ $image->temporaryUrl() }}" class="object-cover w-full h-full">
                        </div>
                    @endforeach
                @endif
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                Speichern
            </button>
        </form>

    </x-my-form>
</div>

</div>
