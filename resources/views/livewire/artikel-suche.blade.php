<div x-data="{ offen: false }" class="relative w-full">
    <div class="flex items-center border border-gray-300 rounded-2xl px-2 py-2 w-full h-8 m-1 md:m-2 bg-gray-200">
        <input type="text"
            wire:model.live.debounce.500ms="suchbegriff"
            class="flex-grow bg-transparent outline-none ring-0 text-sm placeholder-gray-500 border-none font-bold focus:outline-none focus:ring-0"
            placeholder="Artikel suchen..."
            @focus="offen = true"
            @click.away="offen = false"
        />
        <div class="ml-2 flex items-center">
            @if ($suchbegriff != '')
                @if (count($ergebnisse) === 0)
                    <x-fluentui-thumb-dislike-16 class="text-red-600 w-5" title="keine Ergebnisse"/>
                @else
                    <x-fluentui-thumb-like-16 class="text-ewe-gruen w-5" title='{{ sprintf(">= %d Ergebnisse", count($ergebnisse)) }} ' />
                @endif
            @else
                <x-fluentui-search-16-o class="w-5 " title=""/>
            @endif
        </div>
    </div>



    <ul x-show="offen" x-transition
    class="absolute z-10 w-[400px] max-h-[70vh] overflow-y-auto bg-white border border-gray-300 mt-1 rounded shadow">


        @foreach($ergebnisse as $artikel)
            <li class="px-4 py-2 hover:bg-gray-100 flex items-center space-x-2 cursor-pointer">
                <div class="flex flex-col ">
                      <div class="flex flex-row items-center">
                        <div class="mr-2">
                            @php
                            $bilder = imageExistsAll($artikel['artikelnr']);
                            @endphp

                            <x-product-image-small :images="$bilder" size="75" artikelnr="{{ $artikel['artikelnr'] }}" beschreibung="{{ $artikel['bezeichnung'] }}" />
                        </div>
                        @php
                            $anr = $artikel['artikelnr'];
                        @endphp

                        <div class="relative group text-gray-500 pr-2">
                            <x-fluentui-info-12-o class="w-5" />
                            @if (!empty(trim($artikel['langtext'])))
                                <span class="absolute hidden group-hover:block bg-gray-700 text-white text-xs rounded py-1 px-2 w-80 -mt-8 z-10">
                                    {!! $artikel['langtext'] !!}
                                </span>
                            @endif

                        </div>
                        <div class="flex flex-row">
                            <x-nav-link :href="route('shop',[ 'activeTab' => 'suche', 'artikel' => $anr, 'suchBezeichnung' => $suchbegriff] )"  wire:navigate>

                            <div class="text-sm text-gray-600"><span class="text-sm font-bold">{{ $artikel['artikelnr'] }}</span> - {{ $artikel['bezeichnung'] }}</div>
                            </x-nav-link>
                        </div>
                    </div>

                </div>
            </li>
        @endforeach
    </ul>
</div>
