
    <!--  Fensterfarbe ursprünglich border-2 border-blue-100 bg-blue-200 shadow-slate-600  ring-4 ring-blue-200 rounded-md shadow-2xl -->
    <div class="flex fixed top-0 bg-opacity-60 item-center w-full h-full bg-slate-100 backdrop-blur-[2px]"
        x-show="showFavoritBearbeitenForm" x-cloak
        x-on:click.self="showFavoritBearbeitenForm = false"
        x-on:keydown.escape.window="showFavoritBearbeitenForm = false"> <!-- gesamtes Fenster backdrop-blur-[2px] -->

        <div {{ $attributes->merge(['class' => ($width ?? 'w-8/12') . ' m-auto  flatwhite']) }}

            x-data="{ isDisabled: true }"


            x-init="$watch('$wire.isModified', value => isDisabled = false);">  <!-- Abfragefenster Fenster -->

            <div class="m-2">
                <form wire:submit.prevent="saveFavorit">
                    @csrf
                    <input id="favoritId" type="hidden" wire:model="favoritId">
                    <div class="flex flex-col space-y-2">
                        <div class="font-bold text-xl border-b-2 border-b-[#CDD503]">
                            @if ($this->isModified)
                               Favorit bearbeiten:
                            @else
                                Favorit anlegen:
                            @endif
                        </div>


                        <div class="mt-4 flex flex-col items-center justify-between h-full space-y-2">
                            <div class="flex flex-row h-8 w-full items-center">
                                <div class="w-2/6">
                                    Name:
                                </div>
                                <div class="w-4/6">
                                    <input type="text" wire:model="favoritName"
                                        class="w-full h-6 border border-gray-500 rounded bg-white">
                                </div>
                            </div>

                            <div class="flex flex-row h-8 w-full items-center">
                                <div class="w-2/6">
                                    Nur für mich:
                                </div>
                                <div class="w-4/6">
                                    <input type="checkbox" wire:model="favoritUser">
                                </div>
                            </div>
                        </div>




                        <div class="flex flex-row items-center">
                            <div class="w-2/6">
                                &nbsp;
                            </div>
                            <div class="w-4/6">
                                <button type="submit" class="py-2 px-4 border border-gray-400 bg-ewe-gruen rounded-md">

                                    @if ($this->isModified)
                                        Ändern
                                    @else
                                        Speichern
                                    @endif
                                </button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
