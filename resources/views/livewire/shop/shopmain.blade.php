<div>


    <div class="w-11/12 m-auto" x-data="{ showForm: @entangle('showForm') }" x-on:click.self="showForm = false"
        x-on:keydown.escape.window="showForm = false">

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>
        @endpush
        <div class="z-0">

        @if (Auth::user())
            @if (Auth::user()->isAdmin())

            <label for="sortiment" class="block text-sm font-medium text-gray-700">Wähle ein Sortiment</label>
            <select id="sortiment" wire:model.live="sortiment"
                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="BE">BE</option>
                <option value="EWE">EWE</option>
                <option value="WN">WN</option>
                <option value="TK">TK</option>
                <option value="OOWV">OOWV</option>
                <option value="EWE & WN">EWE, WN</option>
                <option value="WN & TK">WN, TK</option>
                <option value="EWE & TK">EWE, TK</option>
                <option value="BE & TK">BE, TK</option>
            </select>
            @endif

        @endif

            
            <div class="flex w-full align-top">
                <div class="w-full">
                    <div class="flex flex-col">
                        <div class="flex flex-row mr-4 mb-2 ">
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

                        <div class="flex flex-row ">

                            <!-- SPALTE 1 -->

                            @if ($activeTab === 'tab1')

                                <div class="p-3 w-1/3 flatwhite">
                                    <div class="text-base font-bold  text-sky-600 border-b border-sky-600 h-6 items-center">
                                        Alle Warengruppen aus ihrem Sortiment
                                    </div>
                                    <!-- SCROLLCONTAINER -->
                                    <div class="flex flex-col h-[calc(100vh-250px)] overflow-y-auto">
                                        <!-- Neue Anpassung hier -->
                                        <div class="text-sm w-full max-h-full pt-2 truncate ...">
                                            <ul class="ml-5 list-image-none">
                                                @foreach ($warengruppen as $wg)
                                                    <li>
                                                        <a href="#"
                                                            wire:click.prevent="clickWarengruppe('{{ $wg->wgnr }}', '{{ $sortiment }}')"
                                                            class="hover:underline ">
                                                            <span
                                                                @if ($aktiveWarengruppe == $wg->wgnr) class="text-3xl font-bold text-sky-600" @endif>
                                                                {{ $wg->bezeichnung }}
                                                            </span>
                                                        </a>
                                                        ({{ $wg->artikel_count }})
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>



                            @endif

                            @if ($activeTab === 'tab2')
                                <div class="w-1/3 p-2 mb-4 align-top flatwhite">
                                    <div class="text-base font-bold text-sky-600 border-b border-sky-600">
                                        Suche</div>
                                    <p>Inhalt: Hier kannst du nach Artikeln suchen.</p>
                                </div>
                            @endif

                            @if ($activeTab === 'tab3')
                                <div class="w-1/3 p-3 mb-4 align-top flatwhite">
                                    <div class="text-base font-bold text-sky-600 border-b border-sky-600">
                                        Favoriten</div>
                                    <p>Inhalt: Deine Favoriten werden hier angezeigt.</p>
                                </div>
                            @endif

                            @if ($activeTab === 'tab4')
                                <div class="w-1/3 p-3 mb-4 align-top  flatwhite ">
                                    <div class="text-base font-bold text-sky-600 border-b border-sky-600">
                                        Schnellerfassung</div>
                                    @livewire('schnellerfassungComponent', ['sortiment' => $sortiment])
                                </div>
                            @endif
                            @if ($activeTab === 'tab5')
                                <div class="w-1/3 ">

                                    @livewire('WarenkorbComponent', ['sortiment' => $sortiment])

                                </div>
                            @endif


                            <!-- SPALTE 2 -->

                            @if ($activeTab != 'tab5')
                                <div class="w-2/3 ml-2 flatwhite ">
                                    <div class="flex flex-col h-[calc(100vh-205px)] overflow-auto " id="tab5">
                                        <div class="w-full max-h-full p-3 mb-2">
                                            @livewire('shopartikellistecomponent', ['quantities' => $quantities])
                                        </div>

                                    </div>
                                </div>
                            @else
                                <div class="w-2/3 ml-2 mb-4 align-top  flatwhite ">
                                    <div class="flex flex-col h-[calc(100vh-5vh)] overflow-auto">
                                        <div class="w-full max-h-full p-3 mb-2">
                                            @livewire('ShopPositionenComponent')
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>

        </div>
        <x-my-form class="z-10">

            <form>
                @csrf

                @if ($mArtikel && $mArtikel->artikelnr)
                    <div>
                        <div class="text-lg text-sky-600 border-b-2 border-sky-600">Artikel: {{ $mArtikel->artikelnr }}
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
