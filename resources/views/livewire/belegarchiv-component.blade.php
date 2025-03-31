<div x-data="{ zeigeMessage: @entangle('zeigeMessage'), }" >
    <div class=" w-11/12 text-sm m-auto">
        <div class="flex flex-col w-full">

            <div class="flatwhite mb-2 py-6 px-6 sticky top-0 z-20  text-sky-600 font-bold text-xl flex justify-between items-center">
                <div>Archivierte Belege vom letzten halben Jahr</div>
                <div class="text-xs">({{ $datumVon->format('Y-m-d') }} - {{ $datumBis->format('Y-m-d') }})</div>

            </div>


            <div class="flex flex-col lg:flex-row ">
                <div class="flex flex-col w-full lg:w-1/3 mb-2 lg:mb-0 flatwhite p-2  overflow-x-scroll  lg:h-[calc(100vh-280px)] overflow-hidden mr-2">

                    <div class="sticky top-0 z-20 bg-white text-sky-600 font-bold py-1 text-base ">
                        Aufträge
                    </div>


                    <div class="overflow-y-auto h-full">

                        <!-- Fixierte Spaltenüberschrift -->
                        <div class="sticky top-[0px] z-10 bg-white flex flex-row text-sky-600 font-bold text-sm py-1 border-b border-sky-600">
                            <div class="w-1/3">Nr.</div>
                            <div class="w-1/3 flex flex-row">
                                <div>
                                    Datum
                                </div>
                                <x-fluentui-arrow-sort-down-lines-16-o class="h-5 w-5" />
                            </div>
                            <div class="w-1/3 text-right pr-1">Nettowert</div>
                        </div>

                        <div class="overflow-y-scroll lg:overflow-y-visible ">
                            @if (!empty($belegeTyp[4]))
                                @foreach ($belegeTyp[4] as $dok)
                                    <a href="#" wire:click="loadBeleg('{{ $dok['nr'] }}')">
                                        <div class="flex flex-row hover:bg-[#CDD503] py-0.5">

                                            <div class="w-1/3 min-w-20 ">
                                                {{ $dok['nr'] }}
                                            </div>
                                            <div class="w-1/3 min-w-20" title="{{ $dok['nr'] }}">
                                                {{ $dok['datum']->format('Y-m-d') }}
                                            </div>
                                            <div class="w-1/3 min-w-32 text-right pr-1">
                                                {{ number_format($dok['netto'], 2, ',', '.') }} €
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex flex-col w-full lg:w-1/3 mb-2 lg:mb-0 flatwhite p-2 lg:h-[calc(100vh-280px)] overflow-hidden">

                    <!-- Fixierte Überschrift -->
                    <div class="sticky top-0 z-20 bg-white text-sky-600 font-bold py-1 text-base ">
                        Lieferscheine
                    </div>

                    <!-- Scrollbarer Bereich -->
                    <div class="overflow-y-auto h-full">

                        <!-- Fixierte Spaltenüberschrift -->
                        <div class="sticky top-[0px] z-10 bg-white flex flex-row text-sky-600 font-bold text-sm py-1 border-b border-sky-600">
                            <div class="w-1/2">Nr.</div>
                            <div class="w-1/2 flex flex-row">
                                <div>
                                    Datum
                                </div>
                                <x-fluentui-arrow-sort-down-lines-16-o class="h-5 w-5" />
                            </div>
                        </div>

                        <!-- Inhalt -->
                        <div class="divide-y">
                            @if (!empty($belegeTyp[2]))
                                @foreach ($belegeTyp[2] as $dok)
                                    <a href="#" wire:click="loadBeleg('{{ $dok['nr'] }}')">
                                        <div class="flex flex-row hover:bg-[#CDD503] py-0.5">
                                            <div class="w-1/2">{{ $dok['nr'] }}</div>
                                            <div class="w-1/2">{{ $dok['datum']->format('Y-m-d') }}</div>
                                        </div>
                                    </a>
                                @endforeach
                            @endif
                        </div>

                    </div>
                </div>



                <div class="flex flex-col w-full lg:w-1/3 flatwhite p-2  overflow-x-scroll  lg:h-[calc(100vh-280px)] overflow-hidden  lg:ml-2">

                    <div class="sticky top-0 z-20 bg-white text-sky-600 font-bold py-1 text-base ">
                        Rechnungen / Gutschriften
                    </div>


                    <div class="overflow-y-auto h-full">

                        <!-- Fixierte Spaltenüberschrift -->
                        <div class="sticky top-[0px] z-10 bg-white flex flex-row text-sky-600 font-bold text-sm py-1 border-b border-sky-600">
                            <div class="w-1/3">Nr.</div>
                            <div class="w-1/3 flex flex-row">
                                <div>
                                    Datum
                                </div>
                                <x-fluentui-arrow-sort-down-lines-16-o class="h-5 w-5" />
                            </div>
                            <div class="w-1/3 text-right pr-1">Nettowert</div>
                        </div>

                        <div class="overflow-y-scroll lg:overflow-y-visible ">
                            @if (!empty($belegeTyp[1]))
                            @foreach ($belegeTyp[1] as $dok)
                                <a href="#" wire:click="loadBeleg('{{ $dok['nr'] }}')">
                                    <div class="flex flex-row hover:bg-[#CDD503] py-0.5">

                                        <div class="w-1/3 min-w-20 ">
                                            {{ $dok['nr'] }}
                                        </div>
                                        <div class="w-1/3 min-w-20" title="{{ $dok['nr'] }}">
                                            {{ $dok['datum']->format('Y-m-d') }}
                                        </div>
                                        <div class="w-1/3 min-w-32 text-right pr-1">
                                            {{ number_format($dok['netto'], 2, ',', '.') }} €
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <x-my-message :titel="$messageTitel" :hinweis="$messageHinweis"/>

</div>
