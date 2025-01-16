<div>

    <div class="p-4 w-11/12 m-auto flatwhite" x-data="{ showForm: @entangle('showForm') }" x-cloak x-on:click.self="showForm = false"
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
                            <input type="text" id="kurztext" wire:model="kurztext"
                                class="border rounded p-1 w-full">
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
                                <input type="number" id="kundennr" wire:model="kundennr"
                                    class="border rounded p-1">
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


        <div class="flex flex-row items-center min-w-full mt-2 mb-4">

            <div class="mr-8">
                <button title="Neue Nachricht" class="text-ewe-gruen flex items-center" type="button"
                    wire:click="newMessage()">
                    <x-fluentui-receipt-add-24-o class="h-10" />
                </button>
            </div>
            <div class="w-40 text-xl font-bold ">Nachrichten</div>
        </div>
        <div class="flex flex-col min-w-full">


            <div class="flex flex-row font-bold text-sky-600 border-b border-sky-600 items-end">
                <div class="w-[6%] px-2 border-r text-center">
                    <span title="Priorität">Prio.</span>
                </div>

                <div class="w-4/12 px-2 border-r">
                    Kurztext
                </div>

                <div class="w-3/12 px-2 border-r">
                    Zeitraum
                </div>

                <div class="w-[6%] px-2 border-r text-cente group">
                    <span title="Auf Startseite anzeigen.">Auf<br>Starts.</span>
                </div>

                <div class="w-[6%] px-2 border-r text-center">
                    <span title="In Bestellbestätigungsmails einfügen.">In<br>Mail.</span>
                </div>

                <div class="w-[6%] px-2 border-r text-center">
                    <span title="Nur angemeldete Benutzer sehen die Nachricht.">Mit<br>Login.</span>
                </div>


                <div class="w-1/12 px-2 ">
                    Aktion
                </div>
            </div>


            @foreach ($nachrichten as $nachricht)
                <div class="hover:bg-[#CDD503]">
                <div class="flex flex-row pt-1 border-b ">
                    <div class="min-w-6 w-[6%] px-2 border-r flex justify-center">
                        @if ($nachricht->prioritaet === 'hoch')
                            <span class="text-red-600 "><x-fluentui-important-24 class="h-5  bg-white rounded-md" /></span>
                        @elseif ($nachricht->prioritaet === 'mittel')
                            <span class="text-ewe-gruen  "><x-fluentui-important-24-o class="h-5 bg-white rounded-md" /></span>
                        @else
                            <span class="text-ewe-gruen  ">&nbsp;</span>
                        @endif
                    </div>

                    <div class="w-4/12 px-2 border-r font-bold">
                        {{ $nachricht->kurztext }}
                    </div>

                    <div class="w-3/12 px-2 border-r">
                        {{ $nachricht->getVonBisStr() }}
                    </div>

                    <div class="min-w-6 w-[6%] px-2 border-r flex justify-center">
                        @if ($nachricht->startseite === true)
                            <x-fluentui-square-12 class="h-5" title="Wird auf Anmeldeseite angezeigt." />
                        @else
                            <x-fluentui-square-12-o class="h-5" title="Wird nicht auf Anmeldeseite angezeigt." />
                        @endif
                    </div>

                    <div class="min-w-6 w-[6%] px-2 border-r flex justify-center">
                        @if ($nachricht->mail === true)
                            <x-fluentui-square-12 class="h-5" title="Wird mit der Bestellbestätigung versendet."/>
                        @else
                            <x-fluentui-square-12-o class="h-5" title="Wird nicht mit der Bestellbestätigung versendet."/>
                        @endif
                    </div>

                    <div class="min-w-6 w-[6%] px-2 border-r flex justify-center">
                        @if ($nachricht->mitlogin === true)
                            <x-fluentui-square-12 class="h-5" title="Benutzer muss angemeldet sein, um die Nachricht zu sehen." />
                        @else
                            <x-fluentui-square-12-o class="h-5" title="Benutzer muss nicht angemeldet sein, um die Nachricht zu sehen." />
                        @endif
                    </div>


                    <div class="w-1/12 flex flex-col lg:flex-row ">
                        <div class=" px-2">
                            <button
                                class="w-6 border border-gray-400 rounded bg-sky-600 shadow-sm shadow-gray-400 text-white"
                                type="button" wire:click="edit({{ $nachricht->id }})"><x-fluentui-edit-16-o
                                    class="h-5" /></button>
                        </div>

                        <div class=" px-2">
                            <button
                                class="w-6 border border-red-600 rounded bg-red-500 shadow-sm shadow-gray-400 text-white"
                                type="submit" wire:click="delete({{ $nachricht->id }})"><x-fluentui-delete-12-o
                                    class="h-5" /></button>
                        </div>
                    </div>
                </div>
                <div class="flex flex-row border-b-2 border-gray-500 mb-4 text-sm ">
                    <div class="ml-[6%] w-full px-2 max-h-14 overflow-y-scroll">
                        {{ $nachricht->langtext }}
                        @if (!empty($nachricht->links)) <br> {{ $nachricht->links }}@endif;
                    </div>
                </div>
                </div>
            @endforeach

        </div>
    </div>

</div>
