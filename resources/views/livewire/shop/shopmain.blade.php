<div class="">


    <div class="w-11/12 m-auto" x-data="{
        expanded: @entangle('expanded'),
        showForm: @entangle('showForm'),
        showFavoritBearbeitenForm: @entangle('showFavoritBearbeitenForm'),
        showFavoritArtikelForm: @entangle('showFavoritArtikelForm'),
        zeigeMessage: @entangle('zeigeMessage'),
        pending: @js($pendingUpdateSuche)
    }"


    x-init="
        document.addEventListener('livewire:load', () => {
            if (pending) {
                console.log('Pending-Suche erkannt → dispatching updateSuche');
                @this.dispatch('updateSuche')
            }
        });
    "
        x-on:click.self="showForm = false; showFavoritBearbeitenForm = false; showFavoritArtikelForm = false; "
        x-on:keydown.escape.window="showForm = false; showFavoritBearbeitenForm = false; showFavoritArtikelForm = false;">

        <div class="z-0 ">

            <div class="flex w-full align-top">
                <div class="w-full ">
                    <div class="flex flex-col">

                        <!--
                            ****************************************************************************
                            MENÜPUNKTE - Anfang
                            ****************************************************************************
                        -->

                        <div class="flex flex-row mr-4 mb-2">
                            <button wire:click="changeTab('warengruppen')"
                                class="@if ($activeTab === 'warengruppen') tabNewActive @else tabNew @endif">
                                Warengruppen
                            </button>
                            <button wire:click="changeTab('suche')"
                                class="@if ($activeTab === 'suche') tabNewActive @else tabNew @endif">
                                Suche
                            </button>
                            <button wire:click="changeTab('favoriten')"
                                class="@if ($activeTab === 'favoriten') tabNewActive @else tabNew @endif">
                                Favoriten
                            </button>
                            <button wire:click="changeTab('schnellerfassung')"
                                class="@if ($activeTab === 'schnellerfassung') tabNewActive @else tabNew @endif">
                                Schnellerfassung
                            </button>
                            <button wire:click="changeTab('warenkorb')"
                                class="@if ($activeTab === 'warenkorb') tabNewActive @else tabNew @endif">
                                <div class="flex flex-row items-center ">
                                    <x-fluentui-shopping-bag-20-o class="h-6" />

                                    Warenkorb
                                </div>
                            </button>

                            <div wire:loading>
                                <div class="z-50 w-full h-full fixed inset-0 flex items-center justify-center">
                                    <svg class="w-20 h-20 text-[#CDD503] animate-spin"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="100"
                                            stroke="currentColor" stroke-width="4">
                                        </circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <!--
                            ****************************************************************************
                            MENÜPUNKTE - ENDE
                            ****************************************************************************
                        -->


                        <div class="flex flex-col md:flex-row">

                            <!--
                            ****************************************************************************
                            Linker Bereich: Warengruppen - Suche - Favoriten - Schnellerfassung - Warenkorb
                            ****************************************************************************
                            -->

                            @if ($activeTab === 'warengruppen') <!-- Warengruppen -->
                                <div class="p-3 w-full md:w-1/3 flatwhite bg-red-400 h-[calc(100vh-245px)] overflow-hidden">

                                    <div class="text-base font-bold  text-sky-600 border-b border-sky-600 h-6 flex items-center">
                                        <span
                                            werkclass="bg-yellow-400 sm:bg-green-400 md:bg-pink-400 lg:bg-red-400 xl:bg-blue-400 2xl:bg-orange-400">
                                            Alle Warengruppen aus ihrem Sortiment
                                        </span>
                                    </div>
                                    <!-- SCROLLCONTAINER -->
                                    <div class="overflow-scroll h-full">
                                        <!-- Neue Anpassung hier -->
                                        <div class="text-sm w-full  pt-2 ">
                                            <ul class=" list-image-none">
                                                @foreach ($warengruppen as $wg)
                                                    <li
                                                        class="pl-2 hover:underline  hover:bg-[#e3e692] hover:text-sky-600">
                                                        @if ($aktiveWarengruppe != $wg['wgnr'])
                                                        <a href="#"
                                                            wire:click.prevent="clickWarengruppe('{{ $wg['wgnr'] }}')"
                                                            class=" flex items-center justify-between group">

                                                            <span
                                                                @if ($aktiveWarengruppe == $wg['wgnr'])
                                                                    class="text-xl md:text-3xl font-bold text-sky-600 truncate ..."
                                                                @else
                                                                    class="hover:text-sky-600 transition-colors duration-200 truncate ..."
                                                                @endif>
                                                                {{ $wg['bezeichnung'] }}
                                                            </span>
                                                            <span class="text-gray-500 text-sm">({{ $wg['artikel_count'] }})</span>
                                                        </a>
                                                        @else
                                                        <div class=" flex items-center justify-between group">

                                                            <span class="text-xl md:text-3xl font-bold text-sky-600 truncate ...">

                                                               {{ $wg['bezeichnung'] }}
                                                            </span>


                                                            <span class="text-gray-500 text-sm">({{ $wg['artikel_count'] }})</span>
                                                        </div>

                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($activeTab === 'suche') <!-- Suche -->
                                <div class="p-2 mb-4 align-top flatwhite w-full md:w-1/3 h-[calc(100vh-245px)] overflow-y-auto">
                                    <div class="text-base font-bold text-sky-600 border-b border-sky-600 mb-4">
                                        Suche
                                    </div>


                                    <form wire:submit.prevent="updateSuche">
                                        @csrf
                                        <div class="flex flex-col w-full space-y-4">
                                            <div class="flex flex-row items-center">
                                                <div class=" w-5/12 flex flex-col text-right mr-2 ">
                                                    <div>
                                                        Artikelnummer(n):
                                                    </div>
                                                </div>
                                                <div class="w-7/12">
                                                    <input type="text" wire:model="suchArtikelnr"
                                                        class="w-full h-6 border border-gray-500 rounded bg-white">
                                                </div>
                                            </div>
                                            <div class="flex flex-row items-center text-right ">
                                                <div class=" w-5/12 flex flex-col mr-2">
                                                    <div>
                                                        Suchtext(e):
                                                    </div>
                                                </div>
                                                <div class=" w-7/12">
                                                    <input type="text" wire:model="suchBezeichnung"
                                                        class="w-full h-6 border border-gray-500 rounded bg-white">
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                <button type="submit"
                                                    class="ml-2 px-4 py-2 border border-blue-600 bg-sky-600 rounded-md text-white">Suchen</button>
                                            </div>

                                        </div>
                                    </form>

                                    <div class="w-full mt-6 p-4 border border-gray-500 rounded bg-slate-200"
                                        x-data="{ expanded: false }">
                                        <div class="flex flex-row items-center">
                                            <div>
                                                <div class="flex flex-row items-center h-full">
                                                    <div
                                                        class="text-ewe-gruen border border-ewe-gruen rounded-md bg-white mr-2">
                                                        <x-fluentui-info-16 class="w-6" />
                                                    </div>
                                                    <button @click="expanded = !expanded" class="">
                                                        <div class="flex flex-row items-center ">
                                                            <div>
                                                                Hinweise zur Suche
                                                            </div>

                                                            <div x-show="!expanded"
                                                                class="flex flex-row items-center h-full">

                                                                <x-fluentui-caret-right-12-o class="w-6" />
                                                            </div>
                                                            <div x-show="expanded"
                                                                class="flex flex-row items-center h-full ">
                                                                <x-fluentui-caret-up-12-o class="w-6" />
                                                            </div>
                                                        </div>
                                                    </button>
                                                </div>


                                            </div>
                                        </div>

                                        <div class="text-sm " x-show="expanded" x-cloak>
                                            <p class="py-2">Ein oder mehr Suchbegriffe (Zahlen oder Text) können auch
                                                in Teilen eingegeben werden. Suchbegriffe werden duch Leerzeichen
                                                getrennt. </p>
                                            <div class="mb-2"><span class="font-bold">Artikelnummer(n):</span> Bei
                                                mehreren, müssen nicht alle Nummern in einem Artikel vorhanden sein.

                                                <div class="mt-0 text-xs"> Beispiel:</div>
                                                <ul class="pl-2">
                                                    <li class="pl-2">
                                                        312172 => 1 Treffer
                                                    </li>
                                                    <li class="pl-2">
                                                        312172 66390 => 11 Treffer
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="mb-2">
                                                <span class="font-bold">Suchtext(e):</span> Eine oder mehr Begriffe
                                                können eingeben werden.
                                                <div class="mt-0 text-xs"> Beispiel:</div>
                                                <ul class="pl-2">
                                                    <li class="pl-2">
                                                        dN100 => 51 treffer
                                                    </li>
                                                    <li class="pl-2">
                                                        dN100 schieber => 3 Treffer
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="pb-2">
                                                <span class="font-bold">Beide Felder:</span>

                                                <div class="mt-0 text-xs"> Beispiel:</div>
                                                <ul class="pl-2">
                                                    <li class="pl-2">
                                                        Artikelnummer: 753 und Suchtext: dN100 schieber => 2 treffer
                                                    </li>
                                                </ul>
                                                <span class="text-xs">(Erklärung: innerhalb aller Artikel die die Zahl
                                                    753 enthalten, müssen beide Suchwörter vorkommen.)</span>

                                            </div>


                                        </div>

                                    </div>

                                </div>
                            @endif

                            @if ($activeTab === 'favoriten') <!-- Favoriten -->
                                <div x-data="{ openSetting : false }" class=" p-3 mb-4 align-top flatwhite w-full md:w-1/3 h-[calc(100vh-245px)] overflow-hidden">
                                    <div
                                        class="flex flex-row justify-between font-bold text-sky-600 border-b border-sky-60 items-center">
                                        <div class="text-base">
                                            Favoriten
                                        </div>
                                        <div class="flex flex-row space-x-2 text-sm text-sky-500">
                                            <a href="#" wire:click="neuerFavorit" class="hover:bg-[#e3e692] hover:text-sky-600">
                                                <div class="flex flex-row mx-2 items-center space-x-1">
                                                    <div>
                                                        <x-fluentui-quiz-new-20-o class="h-5" />
                                                    </div>
                                                    <div>
                                                        Neu
                                                    </div>
                                                </div>
                                            </a>

                                            <div class="text-sky-500 "> | </div>


                                                <a href="#" @click="openSetting = !openSetting" class="hover:bg-[#e3e692] hover:text-sky-600">
                                                    <div class="flex flex-row mx-2 items-center space-x-1 text-sky-500">
                                                        <div>
                                                            <x-fluentui-settings-16-o class="h-5" />
                                                        </div>
                                                        <div>
                                                            Bearbeiten
                                                        </div>
                                                        </div>
                                                </a>
                                        </div>
                                    </div>

                                    <div class="flex flex-col mt-3  ">

                                        @foreach ($favoriten as $key => $favorit)
                                            <div class="flex flex-row items-center justify-between h-full hover:underline hover:font-bold hover:bg-[#e3e692] hover:text-sky-600 py-1">
                                                <div
                                                    class="flex flex-row w-full items-center ">
                                                    <a href="#" wire:click="selectFavorit({{ $key }})"
                                                        class="h-full flex flex-row w-full items-center">
                                                        <div class="pl-1 pr-2">
                                                            @if ($favorit['user_id'])
                                                                <x-fluentui-person-16-o class="h-5" />
                                                            @else
                                                                <x-fluentui-people-team-16-o class="h-5" />
                                                            @endif
                                                        </div>
                                                        <div class="w-full">
                                                            <span
                                                                @if ($aktiveFavorites == $favorit['id'])
                                                                    class="text-xl md:text-3xl font-bold text-sky-600"
                                                                @else
                                                                    class=""
                                                                @endif>
                                                                {{ $favorit['name'] }}
                                                            </span>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="h-full flex flex-row items-center space-x-2" :class="openSetting ? 'block' : 'hidden'">

                                                @php
                                                    $rolle = session()->get('rolle');
                                                @endphp
                                                @if ( $rolle === 1 || ( $rolle === 0 && Auth::id() === $favorit['user_id']))
                                                    <div class="">
                                                        <a href="#"
                                                            wire:click="abfrageLoeschungFavorit({{ $key }})"
                                                            class="hover:bg-[#e3e692] hover:text-sky-600"
                                                            title="'{{ $favorit['name'] }}' löschen">
                                                            <x-fluentui-delete-16-o class="h-5" />
                                                        </a>
                                                    </div>
                                                    @endif
                                                    <div class="">
                                                        <a href="#"
                                                            wire:click="editFavorit({{ $key }})"
                                                            class="hover:bg-[#e3e692] hover:text-sky-600"
                                                            title="'{{ $favorit['name'] }}' bearbeiten">
                                                            <x-fluentui-settings-16-o class="h-6" />
                                                        </a>
                                                    </div>
                                                @if ($rolle === 1)
                                                    <div class="">
                                                        <a href="#"

                                                            wire:click="favoritenDownload({{ $key }})"
                                                            class="hover:bg-[#e3e692] hover:text-sky-600"
                                                            title="'{{ $favorit['name'] }}' favoriten herunterladen">
                                                            <x-fluentui-arrow-circle-down-right-24-o class="h-6" />
                                                        </a>
                                                    </div>

                                                    <div class="">
                                                        <a href="#"
                                                            x-data
                                                            @click="
                                                                $wire.set('favoritId', {{ $favorit['id'] }});
                                                                $wire.set('showFavoritenPosImportModal', true);
                                                            "
                                                             title="'{{ $favorit['name'] }}' favoriten hochladen"
                                                            class="hover:bg-[#e3e692] hover:text-sky-600">

                                                            <x-fluentui-arrow-circle-up-left-24-o class="h-6" />

                                                        </a>
                                                    </div>
                                                @endif
                                                </div>
                                            </div>
                                        @endforeach

                                    </div>
                                </div>

                            @endif

                            @if ($activeTab === 'schnellerfassung') <!-- Schnellerfassung -->
                                <div class="p-3 mb-4 align-top  flatwhite w-full md:w-1/3 h-[calc(100vh-245px)] overflow-hidden">
                                    <div class="text-base font-bold text-sky-600 border-b border-sky-600">
                                        Schnellerfassung</div>
                                    @livewire('schnellerfassung-component', ['sortiment' => $sortiment])
                                </div>
                            @endif

                            @if ($activeTab === 'warenkorb') <!-- Warenkorb -->
                                <div class="w-full md:w-1/3">

                                    @livewire('warenkorb-component', ['sortiment' => $sortiment])

                                </div>
                            @endif


                            <!-- SPALTE 2 -->

                            @if ($activeTab != 'warenkorb') <!-- Alle Menüpunkte aus Warenkorb -->
                            <div class="w-full md:w-2/3 md:ml-2 flatwhite h-[calc(100vh-245px)] overflow-hidden">
                                <div class="flex flex-col " id="tab5">
                                    <!-- Enthält die Komponente -->
                                    <div class="w-full max-h-full p-3 mb-2">

                                        @livewire('shop-artikelliste-component', ['quantities' => $quantities])

                                    </div>
                                </div>
                            </div>

                            @else
                                <div class="w-full md:w-2/3 md:ml-2 mb-4 align-top  flatwhite   ">
                                    <div class="flex flex-col h-[calc(100vh-245px)] overflow-auto">
                                        <div class="w-full max-h-full p-3 mb-2">
                                            @livewire('shop-positionen-component')
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Ja oder Nein Abfrage
            zeigeJaNeinAbfrage = true
        -->
        <x-modal-ja-nein-abfrage
            text='Soll die Favoritenliste "{{ $favoritName }}" gelöscht werden?'
            comment='Rückgängig ist nicht möglich!'
            onJa="jaBestaetigt"
            onNein="neinBestaetigt"
        />

        <x-favoriten-pos-import-modal />

        <x-my-message :titel="$messageTitel" :hinweis="$messageHinweis" />

        <!-- Artikel zu favorit hinzufügen
            showFavoritArtikelForm = true
        -->
        <x-my-favorit-artikel-form :mArtikel="$favArtikel" :favoriten="$favoriten" :aktiveFavorites="$aktiveFavorites" />


        <!-- Favoriten bearbeiten / anlegen
            showFavoritBearbeitenForm = true
        -->
        <x-my-favorit-bearbeiten-form class="z-11" :width="'w-4/12'" />




        <x-my-form :isModified="$isModified" class="max-h-[70vh] z-40 overflow-hidden">

            <form class="" wire:submit.prevent="InBasket">
                @csrf

                @if ($mArtikel && $mArtikel->artikelnr)
                    @php
                        $pos = $aPositions[0];
                    @endphp
                    <div class="flex flex-row items-center justify-between border-b-2 border-sky-600">

                        <div class="flex flex-row items-center">
                            <div class="mr-2">
                                <a href="#" wire:click.prevent="favoritArtikelForm( '{{ $mArtikel->artikelnr }}')" class="text-gray-300 hover:text-yellow-500 group">
                                    @if ($pos['is_favorit'])
                                        <x-fluentui-star-emphasis-20 class="text-yellow-500 w-5" />
                                    @else
                                        <!-- Normaler Zustand -->
                                        <x-fluentui-star-28-o class="w-5 group-hover:hidden" />
                                        <!-- Hover-Zustand -->
                                        <x-fluentui-star-28-o class="w-5 hidden group-hover:block" />
                                    @endif
                                </a>
                            </div>
                            <div class="text-lg text-sky-600">
                                Artikel: {{ $mArtikel->artikelnr }} - {{ $mArtikel->bezeichnung }}
                            </div>
                        </div>
                        <div>
                            <button @click="showForm = false;">
                                <x-fluentui-dismiss-square-20-o class="h-6" />
                            </button>
                        </div>
                    </div>
                    <div class="flex flex-row mb-4  w-full">

                        <div class="relative basis-3/4 p-2  ">
                            <div class="absolute top-2 right-2 float-left">


                                @php
                                    $bilder = imageExistsAll($mArtikel->artikelnr);
                                @endphp

                                <x-product-image :images="$bilder" size="250" artikelnr="{{ $mArtikel->artikelnr }}" beschreibung="{{ $mArtikel->bezeichnung }}" />


                            </div>
                            Warengruppe: {{  $aktiveWarengruppeBezeichung }}<br>


                            <br>
                            <span class="text-base max-h-4">{!! $mArtikel->langtext !!}</span>

                            <!-- Bild in der oberen rechten Ecke -->

                        </div>
                        <div class="basis-1/4 p-2">
                            <div class="flex flex-col items-end">
                                <div class="basis-1 text-red-800 text-lg">
                                    {{ number_format($mArtikel->vkpreis, 2, ',', '.') }} € / {{ $mArtikel->einheit }}
                                </div>
                                <br>

                                <div class="flex flex-row">
                                    <div class="basis-1 text-center flex ">


                                        <div x-data="{ quantity: {{ $pos['menge'] }}, loop:0 }"
                                            @basket-cleared.window="quantity = 0"
                                            x-init="quantity = {{ $pos['menge'] }}"
                                            class="flex items-center overflow-hidden w-24 py-0 border border-gray-400 rounded">

                                            <button type="button" @click="quantity = Math.max(0, quantity - 1); $wire.set('aPositions.0.menge', quantity)"
                                                class="flex-1 bg-gray-200 text-gray-700 py-0.5 hover:bg-blue-200 h-7 border-r border-r-gray-400">-</button>

                                            <input type="number" min="0" max="1000000" step="1" x-model="quantity" wire:model="aPositions.0.menge"
                                                class="InputMenge px-1 w-14 text-center border-none outline-none text-xs h-7"
                                                @focus="$event.target.select()">

                                            <button type="button" @click="quantity++; $wire.set('aPositions.0.menge', quantity)"
                                                class="flex-1 bg-gray-200 text-gray-700 py-0.5 hover:bg-blue-200 h-7 border-l border-l-gray-400">+</button>

                                            <!-- input type="hidden" name="artikelmenge[{{ $pos['artikelnr'] }}]" x-model="quantity" -->
                                        </div>

                                    </div>
                                    <div class="basis-1 text-xs pt-2 pl-2">

                                        @if ($mArtikel->bestand == 0)
                                            <x-fluentui-vehicle-truck-profile-24-o class="h-7 text-red-500" />
                                        @else
                                            <x-fluentui-vehicle-truck-profile-24 class="h-7 text-[#CDD503]" />
                                        @endif

                                    </div>
                                </div>
                                <div>
                                    <button type="submit" class="w-40 bg-sky-600 text-white mt-2 py-2 rounded-md hover:font-bold shadow-md">
                                        In den Warenkorb
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                    @if (count($aPositions) > 1)
                        <div class="flex flex-col mb-4  w-full">
                            <div class="text-base text-gray-600 border-b border-sky-600">Ersatzartikel

                            </div>
                            <div>
                                @php
                                    $tabFavoritActive = false; // $selectedTab === Tab::arFavoriten;
                                    $favoritenActiveId = 0;
                                @endphp
                                @foreach ($aPositions as $index => $position)
                                    @if ($loop->index === 0)
                                        @continue
                                    @endif
                                    <div wire:key="pos-{{ $position['uid'] }}">
                                        <x-artikel-kurz :pos="$position" :loop="$loop" :tabFavoritActive="$tabFavoritActive" :favoritenActiveId="$favoritenActiveId" />
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    <span class="text-base">Kein Artikel ausgewählt</span><br>
                @endif

            </form>
        </x-my-form>
    </div>

</div>
