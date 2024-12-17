<div>

    <div class="p-4" x-data="{ showForm: @entangle('showForm') }" x-cloak x-on:click.self="showForm = false"
    x-on:keydown.escape.window="showForm = false">

        <x-my-form>
        <form wire:submit.prevent="store">
            <input type="hidden" wire:model="nachrichtId" />
            <div class="flex flex-col p-3">
                <div class="text-xl font-bold mb-4">Nachrichten verwalten/bearbeiten</div>

                <div class="mb-2 flex flex-row items-center ">
                    <div class="w-32 text-right mr-2">
                        <label for="kurztext" class="block font-bold">Kurztext:</label>
                    </div>
                    <div class="w-full">
                        <input type="text" id="kurztext" wire:model="kurztext" class="border rounded p-1 w-full">
                        @error('kurztext')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-4 flex flex-row items-center">
                    <div class="w-32 text-right mr-2">
                        <label for="von" class="block font-bold">Von:</label>
                    </div>
                    <div class="w-full flex flex-row items-center">
                        <div class="w-40">
                            <input type="date" id="von" wire:model="von" class="border rounded p-1">
                            @error('von')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="w-8 text-center mr-2">
                            <label for="bis" class="block font-bold">bis:</label>
                        </div>
                        <div class="w-40">
                            <input type="date" id="bis" wire:model="bis" class="border rounded p-1">
                            @error('bis')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-4 flex flex-row items-start">
                    <div class="w-32 text-right mr-2">
                        <label for="langtext" class="block font-bold">Langtext:</label>
                    </div>
                    <div class="w-full">
                        <textarea id="langtext" wire:model="langtext" class="border rounded p-1 w-full"></textarea>
                        @error('langtext')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-4 flex flex-row items-start">
                    <div class="w-32 text-right mr-2">
                        <label for="links" class="block font-bold">Links:</label>
                    </div>
                    <div class="w-full">
                        <textarea id="links" wire:model="links" class="border rounded p-1 w-full"></textarea>
                        <div class="text-xs">Link => Beschreibung</div>
                        @error('links')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-4 flex flex-row items-center">
                    <div class="w-32 text-right mr-2">
                        <label for="prioritaet" class="block font-bold">Priorität:</label>
                    </div>
                    <div class="w-full">
                        <select id="prioritaet" wire:model="prioritaet" class="border rounded p-2 w-full">
                            <option value="normal">Normal</option>
                            <option value="mittel">Mittel</option>
                            <option value="hoch">Hoch</option>
                        </select>
                        @error('prioritaet')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-4 flex flex-row items-center">
                    <div class="w-32 text-right mr-2">
                        <label for="startseite" class="block font-bold">Kopfzeile:</label>
                    </div>
                    <div class="w-full flex flex-row items-center">
                        <div class="w-8">
                            <input type="checkbox" id="startseite" wire:model="startseite">
                        </div>

                        <div class="w-48 text-right mr-2">
                            <label for="mail" class="block font-bold">In Bestätigungsmail:</label>
                        </div>
                        <div class="w-full">
                            <input type="checkbox" id="mail" wire:model="mail">
                        </div>
                    </div>

                </div>

                <div class="mb-4 flex flex-row items-center">
                    <div class="w-32 text-right mr-2">
                        <label for="mitlogin" class="block font-bold">Mit Login:</label>
                    </div>
                    <div class="w-full flex flex-row items-center">
                        <div class="w-8">
                            <input type="checkbox" id="mitlogin" wire:model="mitlogin">
                        </div>
                        <div class="">
                            <label for="kundennr" class="font-bold mr-2">Kundennummer:</label>
                        </div>
                        <div class="">
                            <input type="number" id="kundennr" wire:model="kundennr" class="border rounded p-1">
                            @error('kundennr')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="text-right ml-auto w-full">
                    <button type="submit" class="bg-sky-600 text-white px-4 py-2 rounded">Speichern</button>
                </div>

            </div>

        </form>
    </x-my-form>


        <div class="flex flex-row items-center min-w-full mt-6 mb-4">

            <div class="mr-8">
                <button title="Neue Nachricht" class="text-ewe-gruen flex items-center" type="button"
                    wire:click="newMessage()">
                    <x-fluentui-receipt-add-24-o class="h-10"/>
                </button>
            </div>
            <div class="w-40 text-xl font-bold ">Nachrichten</div>
        </div>
        <div class="flex flex-col min-w-full">

            @foreach ($nachrichten as $nachricht)
                <div class="flex flex-col flatwhite mb-3">
                    <div class="flex flex-row w-full">
                        <div class="flex flex-col w-6/12">
                            <div class="flex flex-row w-full">
                                <div class="p-2 w-20 text-right  text-gray-500">Kurztext: </div>
                                <div class="p-2 w-auto">{{ $nachricht->kurztext }}</div>
                            </div>
                            <div class="flex flex-row w-full">
                                <div class="p-2 w-20 text-right text-gray-500">Langtext: </div>
                                <div class="p-2 w-auto">{{ $nachricht->langtext }}</div>
                            </div>
                            <div class="flex flex-row w-full">
                                <div class="p-2 w-20 text-right text-gray-500">Links: </div>
                                <div class="p-2 w-auto">{{ $nachricht->links }}</div>
                            </div>
                        </div>
                        <div class="flex flex-col w-4/12">
                            <div class="flex flex-row w-full">
                                <div class="p-2 w-40 text-right text-gray-500">Priorität: </div>
                                <div class="p-2 w-auto">
                                    @if ($nachricht->prioritaet === 'normal')
                                        Normal
                                    @elseif ($nachricht->prioritaet === 'mittel')
                                        Mittel
                                    @else
                                        Hoch
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-row w-full">
                                <div class="p-2 w-40 text-right text-gray-500">Zeitraum: </div>
                                <div class="p-2 w-auto">{{ $nachricht->getVonBisStr() }}</div>
                            </div>
                            <div class="flex flex-row w-full">
                                <div class="p-2 w-40 text-right text-gray-500">Kopfzeile / In Bestätigungsmail: </div>
                                <div class="p-2 w-auto">{{ $nachricht->startseite ? 'Ja' : 'Nein' }} / {{ $nachricht->mail ? 'Ja' : 'Nein' }}</div>
                            </div>

                            <div class="flex flex-row w-full">
                                <div class="p-2 w-40 text-right text-gray-500">Mit Login: </div>
                                <div class="p-2 w-auto">{{ $nachricht->mitlogin ? 'Ja' : 'Nein' }}</div>
                            </div>


                        </div>

                        <div class="flex flex-col w-2/12">
                            <div class="m-2 pr-4 w-full text-right">
                                <button class="w-32 border border-gray-400 rounded bg-sky-600 shadow-sm shadow-gray-400 text-white" type="button"
                                    wire:click="edit({{ $nachricht->id }})">Bearbeiten</button>
                            </div>

                            <div class="m-2 pr-4 w-full text-right">
                                <button
                                    class="w-32 border border-red-600 rounded bg-red-500 shadow-sm shadow-gray-400 text-white"
                                    type="submit" wire:click="delete({{ $nachricht->id }})">Löschen</button>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach

        </div>
    </div>

</div>
