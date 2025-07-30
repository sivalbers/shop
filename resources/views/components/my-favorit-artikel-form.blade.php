<div class="flex fixed top-0 item-center w-full h-full bg-slate-100/60 backdrop-blur-[2px] z-50"
    x-show="showFavoritArtikelForm" x-cloak x-on:click.self="showFavoritArtikelForm = false"
    x-on:keydown.escape.window="showFavoritArtikelForm = false"> <!-- gesamtes Fenster backdrop-blur-[2px] -->

    <div class="w-11/12 sm:w-4/12 m-auto flatwhite" x-data="{ isDisabled: true }" x-init="$watch('$wire.isModified', value => isDisabled = false);">
        <!-- Abfragefenster Fenster -->

        <div class="m-2">
            <form wire:submit.prevent="saveFavoritArtikel">
                @csrf
                <input id="favoritId" type="hidden" wire:model="artikelnr">
                <div class="flex flex-col space-y-2">
                    <div class="font-bold text-xl ">
                        Artikel zu Favoriten hinzufügen / entfernen
                    </div>
                    @if ($mArtikel)
                        <div class="font-bold text-base border-b border-b-[#CDD503]">
                            {{ $mArtikel->artikelnr }} ::: {{ $mArtikel->bezeichnung }}
                        </div>
                    @endif

                    @foreach ($favoriten as $key => $favorit)
                        <div class="flex flex-row items-center justify-between h-full ">
                            <div class="pl-2 flex flex-row h-8 w-full items-center hover:underline hover:font-bold hover:bg-[#e3e692] hover:text-sky-600">
                                <label for="favoritenIDs.{{ $favorit['id'] }}" class="flex flex-row items-center w-full cursor-pointer">
                                    <input type="checkbox" wire:model="favoritenIDs.{{ $favorit['id'] }}" id="favoritenIDs.{{ $favorit['id'] }}"
                                        value="{{ $favorit['id'] }}" class="mr-2">

                                    <div class="pl-1 pr-2 @if ($aktiveFavorites === $favorit['id']) text-xl md:text-3xl font-bold text-sky-600 @endif">
                                        @if ($favorit['user_id'])
                                            <x-fluentui-person-16-o class="h-5" />
                                        @else
                                            <x-fluentui-people-team-16-o class="h-5" />
                                        @endif
                                    </div>
                                    <div class="w-full">
                                        <span
                                            @if ($aktiveFavorites === $favorit['id'])
                                                class="text-xl md:text-3xl font-bold text-sky-600"
                                            @endif>
                                            {{ $favorit['name'] }}
                                        </span>
                                    </div>
                                </label>
                            </div>

                            <div class="h-full flex flex-row">
                                <div class="">
                                    <a href="#" wire:click="editFavorit({{ $key }})"
                                        class="hover:bg-[#e3e692] hover:text-sky-600">
                                        <x-fluentui-settings-16-o class="h-6" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="flex flex-row w-full items-center justify-between ">
                        <div class="">
                            &nbsp;
                        </div>
                        <div class=" ">
                            <button type="submit"
                                class="py-1 px-4 border border-gray-400 bg-ewe-gruen rounded-md">Speichern</button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
