<div class="">


    <div class="w-11/12 m-auto" x-data="{
        expanded: @entangle('expanded'),
        showForm: @entangle('showForm'),
        showFavoritForm: @entangle('showFavoritForm'),
        zeigeFavoritPosForm: @entangle('zeigeFavoritPosForm'),
        zeigeMessage: @entangle('zeigeMessage'),
    }"
        x-on:click.self="showForm = false; showFavoritForm = false; zeigeFavoritPosForm = false; "
        x-on:keydown.escape.window="showForm = false; showFavoritForm = false; zeigeFavoritPosForm = false;">

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
                            <button wire:click="changeTab('tab1')"
                                class="@if ($activeTab === 'tab1') tabNewActive @else tabNew @endif">
                                Warengruppen
                            </button>
                            <button wire:click="changeTab('tab2')"
                                class="@if ($activeTab === 'tab2') tabNewActive @else tabNew @endif">
                                Suche
                            </button>
                            <button wire:click="changeTab('tab3')"
                                class="@if ($activeTab === 'tab3') tabNewActive @else tabNew @endif">
                                Favoriten
                            </button>
                            <button wire:click="changeTab('tab4')"
                                class="@if ($activeTab === 'tab4') tabNewActive @else tabNew @endif">
                                Schnellerfassung
                            </button>
                            <button wire:click="changeTab('tab5')"
                                class="@if ($activeTab === 'tab5') tabNewActive @else tabNew @endif">
                                <div class="flex flex-row items-center ">
                                    <x-fluentui-shopping-bag-20-o class="h-6" />

                                    Warenkorb
                                </div>
                            </button>

                            <div wire:loading.delay.shortest>
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

                            @if ($activeTab === 'tab1') <!-- Warengruppen -->
                                <div class="p-3 w-full md:w-1/3 flatwhite bg-red-400">

                                    <div class="text-base font-bold  text-sky-600 border-b border-sky-600 h-6 flex items-center">
                                        <span
                                            werkclass="bg-yellow-400 sm:bg-green-400 md:bg-pink-400 lg:bg-red-400 xl:bg-blue-400 2xl:bg-orange-400">
                                            Alle Warengruppen aus ihrem Sortiment
                                        </span>
                                    </div>
                                    <!-- SCROLLCONTAINER -->
                                    <div class=" h-[30vh] md:h-[calc(100vh-205px)]  overflow-scroll">
                                        <!-- Neue Anpassung hier -->
                                        <div class="text-sm w-full  pt-2 ">
                                            <ul class=" list-image-none">
                                                @foreach ($warengruppen as $wg)
                                                    <li
                                                        class="pl-2 hover:underline  hover:bg-[#e3e692] hover:text-sky-600">
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
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($activeTab === 'tab2') <!-- Suche -->
                                <div class="p-2 mb-4 align-top flatwhite w-full md:w-1/3">
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

                                        <div class="text-sm" x-show="expanded" x-cloak>
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

                            @if ($activeTab === 'tab3') <!-- Favoriten -->
                                <div class=" p-3 mb-4 align-top flatwhite w-full md:w-1/3">
                                    <div
                                        class="flex flex-row justify-between font-bold text-sky-600 border-b border-sky-600">
                                        <div class="text-base">
                                            Favoriten
                                        </div>
                                        <div class="flex flex-row">

                                            <div>
                                                <a href="#" wire:click="neuerFavorit">
                                                    <x-fluentui-quiz-new-20-o class="h-5" />
                                                </a>
                                            </div>
                                            <div>
                                                <a href="#" wire:click="neuerFavorit">
                                                    NEU
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-col mt-3 ">

                                        @foreach ($favoriten as $key => $favorit)
                                            <div class="flex flex-row items-center justify-between h-full ">
                                                <div
                                                    class="flex flex-row h-14 w-full items-center hover:underline hover:font-bold hover:bg-[#e3e692] hover:text-sky-600">
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
                                                                @if ($aktiveFavorites === $favorit['id']) class="text-xl md:text-3xl font-bold text-sky-600"
                                                        @else
                                                            class="" @endif>
                                                                {{ $favorit['name'] }}
                                                            </span>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="h-full flex flex-row">
                                                    <div class="">
                                                        <a href="#"
                                                            wire:click="editFavorit({{ $key }})"
                                                            class="hover:bg-[#e3e692] hover:text-sky-600">
                                                            <x-fluentui-settings-16-o class="h-6" />
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                    </div>
                                </div>

                            @endif

                            @if ($activeTab === 'tab4') <!-- Schnellerfassung -->
                                <div class="p-3 mb-4 align-top  flatwhite w-full md:w-1/3">
                                    <div class="text-base font-bold text-sky-600 border-b border-sky-600">
                                        Schnellerfassung</div>
                                    @livewire('schnellerfassung-component', ['sortiment' => $sortiment])
                                </div>
                            @endif

                            @if ($activeTab === 'tab5') <!-- Warenkorb -->
                                <div class="w-full md:w-1/3">

                                    @livewire('warenkorb-component', ['sortiment' => $sortiment])

                                </div>
                            @endif


                            <!-- SPALTE 2 -->

                            @if ($activeTab != 'tab5') <!-- Alle Menüpunkte aus Warenkorb -->
                            <div class="w-full md:w-2/3 md:ml-2 flatwhite">
                                <div class="flex flex-col h-[calc(100vh-205px)] overflow-hidden" id="tab5">
                                    <!-- Enthält die Komponente -->
                                    <div class="w-full max-h-full p-3 mb-2">

                                        @livewire('shop-artikelliste-component', ['quantities' => $quantities])

                                    </div>
                                </div>
                            </div>

                            @else
                                <div class="w-full md:w-2/3 md:ml-2 mb-4 align-top  flatwhite   ">
                                    <div class="flex flex-col h-[calc(100vh-5vh)] overflow-auto">
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

        <x-my-message />


        <x-my-favoritposform :mArtikel="$mArtikel" :favoriten="$favoriten" :aktiveFavorites="$aktiveFavorites" />


        <x-my-favoritform class="z-11" :width="'w-4/12'">

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

                    @if ($favoritId > -1)
                        <div class="h-full flex flex-row">
                            <div class="">
                                <a href="#" wire:click="editFavorit({{ $favoritId }})"
                                    class="hover:bg-[#e3e692] hover:text-sky-600">
                                    <x-fluentui-settings-16-o class="h-6" />
                                </a>
                            </div>
                        </div>
                    @endif


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
        </x-my-favoritform>

        <x-my-form class="z-10">

            <form>
                @csrf

                @if ($mArtikel && $mArtikel->artikelnr)
                    <div>
                        <div class="text-lg text-sky-600 border-b-2 border-sky-600">Artikel:
                            {{ $mArtikel->artikelnr }}
                            - {{ $mArtikel->bezeichnung }} </div>
                    </div>
                    <div class="flex flex-row mb-4  w-full">

                        <div class="relative basis-3/4 p-2  ">
                            <div class="absolute top-2 right-2 float-left">

                                <x-product-image image="blank.png" size="150" class="" />


                            </div>
                            Warengruppe: {{ $aktiveWarengruppeBezeichung }}<br>


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
                                <div class="basis-1 text-xs">
                                    Lagergestand:
                                    <!-- livewire('ShopArtikelBestandComponent', ['artikelnr' => $artikel->artikelnr ]) -->
                                    0 {{ $mArtikel->einheit }}
                                </div>
                                <br>
                                <div class="basis-1 text-center flex items-center">
                                    <div x-data="{ quantity: 0 }"
                                        class="flex items-center border border-gray-300 rounded-md overflow-hidden w-24 ">
                                        <button type="button" @click="quantity > 0 ? quantity-- : 0"
                                            class="flex-1 bg-gray-200 text-gray-700 py-1 hover:bg-gray-300">-</button>
                                        <input type="text" x-model="quantity"
                                            class="w-10 text-center border-none outline-none" readonly>
                                        <button type="button" @click="quantity++"
                                            class="flex-1 bg-gray-200 text-gray-700 py-1 hover:bg-gray-300">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col mb-4  w-full">
                        <br>
                        <br>
                        <br>
                        <br>
                        <div class="text-base text-gray-600 border-b border-sky-600">Ersatzartikel

                        </div>
                        <div>
                            - aktuell keine Artikel hinterlegt -
                        </div>
                    </div>
                @else
                    <span class="text-base">Kein Artikel ausgewählt</span><br>
                @endif

            </form>
        </x-my-form>
    </div>

</div>
