<div x-data="{ zeigeMessage: @entangle('zeigeMessage'), }" >
    <div class=" w-11/12 text-sm m-auto">
        <div class="flex flex-col lg:flex-row ">
        <!-- ANFANG -->

            <div class="flex flex-col w-full lg:w-2/5 flatwhite p-2 h-[calc(100vh-202px)]">
                <!-- Überschrift 'Bestellungen' -->
                <div class="text-sky-600 font-bold text-xl py-0.5">
                    {{ (session()->get('punchout') === 1) ? 'Alte Warenkörbe' : 'Bestellungen' }}

                </div>

                <!-- Tabellenüberschrift fixiert -->
                <div class="flex flex-row text-sky-600 font-bold py-0.5 border-b border-sky-600 bg-white z-10">
                    <div class="w-[11%] text-right pr-2 lg:pr-4 min-w-14">
                        Nr.
                    </div>
                    <div class="w-[18%] min-w-20">
                        Datum
                    </div>
                    <div class="w-[25%] -ml-2 lg:ml-1 min-w-20">
                        Status
                    </div>
                    <div class="w-[34%] min-w-32">
                        Besteller
                    </div>
                    <div class="w-[20%] text-right pr-4 lg:pr-1 min-w-24 mr-4">
                        Betrag
                    </div>
                </div>

                <!-- Scrollbarer Bereich -->
                <div class="overflow-y-auto flex-1">
                    @foreach ($bestellungen as $bestellung)
                        <a href="#" wire:click="loadPositionen('{{ $bestellung['nr'] }}')">
                            <div
                                class="flex flex-row @if ($bestellung['nr'] == $activeBestellung['nr']) font-bold text-white bg-sky-600 @endif
                                            hover:bg-[#CDD503] py-0.5">
                                <div class="w-[11%] text-right pr-2 lg:pr-4 min-w-14">
                                    {{ $bestellung['nr'] }}
                                </div>
                                <div class="w-[18%] min-w-20">
                                    {{ $bestellung['datum']->format('d.m.Y') }}
                                </div>
                                <div class="w-[25%] min-w-20" title="{{ $bestellung['erpid'] }}">
                                    {{ $bestellung['status'] }}
                                </div>
                                <div class="w-[34%] min-w-32">
                                    {{ $bestellung['besteller'] }}
                                </div>
                                <div class="w-[20%] text-right pr-2 lg:pr-1 min-w-24">
                                    {{ number_format($bestellung['gesamtbetrag'], 2, ',', '.') }} €
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

<!-- ENDE -->


            <div class="flex flex-col w-full lg:w-3/5 mt-2 lg:mt-0 ">
                <div class=" flatwhite p-2 ml-0 lg:ml-2 mb-2">
                    <div class="flex flex-col ">

                        <div class="flex flex-row">
                            <div class="flex flex-row w-1/2 items-center">
                                <div class="text-xl font-bold  text-sky-600 w-28">
                                    {{ (session()->get('punchout') === 1) ? 'Warenkorb:' : 'Bestellung:' }}

                                </div>
                                <div class="text-xl font-bold  text-sky-600">
                                    <div>{{ !empty($activeBestellung) ? $activeBestellung->nr : '' }} </div>
                                </div>
                                @if (Auth::user()->isAdmin())
                                    @if (!empty($activeBestellung))
                                        <div class="border border-red-500 bg-red-100 rounded-md mx-2 px-2 shadow-md">
                                            <button wire:click="bestellungErneutSenden('{{ $activeBestellung->nr }}')">
                                                Nochmal versenden </button>
                                        </div>
                                    @endif
                                @endif
                            </div>
                            <div class="flex flex-row w-1/2">
                                @if (Auth::user()->isAdmin())
                                    <div class="font-bold text-sky-600 w-28">
                                        Entität ID
                                    </div>
                                    <div class="pl-2">
                                        {{ !empty($activeBestellung->erpid) ? $activeBestellung->erpid : '---' }}
                                        (Admin-Info)
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-col ">
                            <div class="flex flex-row">
                                <div class="flex flex-row w-1/2">
                                    <div class="font-bold  text-sky-600 w-28">
                                        Besteller:
                                    </div>
                                    <div class="">
                                        {{ !empty($activeBestellung->user->name) ? $activeBestellung->user->name : '---' }}
                                    </div>
                                </div>
                                <div class="flex flex-row w-1/2">
                                    <div class="font-bold text-sky-600 w-28">
                                        Kundenbestellnr.:
                                    </div>
                                    <div class="pl-2">
                                        {{ !empty($activeBestellung->kundenbestellnr) ? $activeBestellung->kundenbestellnr : '---' }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-row">
                                <div class="flex flex-row w-1/2">
                                    <div class="font-bold text-sky-600 w-28">
                                        Kommission:
                                    </div>
                                    <div class="">
                                        {{ !empty($activeBestellung->kommission) ? $activeBestellung->kommission : '---' }}
                                    </div>
                                </div>

                                <div class="flex flex-row w-1/2 ">
                                    <div class="font-bold text-sky-600 w-28">
                                        Lieferdatum:
                                    </div>
                                    <div class="pl-2">
                                        {{ !empty($activeBestellung->lieferdatum) ? optional($activeBestellung->lieferdatum)->format('d.m.Y') : '---' }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-row">
                                <div class="font-bold text-sky-600 w-28">
                                    Bemerkung:
                                </div>
                                <div class="truncate"
                                    title="{{ !empty($activeBestellung->bemerkung) ? $activeBestellung->bemerkung : '' }}">
                                    {{ !empty($activeBestellung->bemerkung) ? $activeBestellung->bemerkung : '---' }}
                                </div>
                            </div>

                            @if (!empty($activeBestellung))
                                @if ($activeBestellung->rechnungsadresse === $activeBestellung->lieferadresse)
                                    <div class="flex flex-row ">
                                        <div class="font-bold pr-2 text-sky-600 w-28">Rechnungsadr.:</div>
                                        <div class="truncate">{{ $activeBestellung->reAdresse->firma1 }} -
                                            {{ $activeBestellung->reAdresse->strasse }} -
                                            {{ $activeBestellung->reAdresse->plz }}
                                            {{ $activeBestellung->reAdresse->stadt }}</div>
                                    </div>
                                    <div class="flex felx-row">
                                        <div class="font-bold pr-2 text-sky-600  w-28">Lieferadr.:</div>
                                        <div class="text-gray-500">( Wie Rechnungsadresse. )</div>
                                    </div>
                                @else
                                    <div class="flex flex-row">
                                        <div class="font-bold pr-2 text-sky-600  w-28">Rechnungsadr.:</div>
                                        <div class="truncate">{{ $activeBestellung->reAdresse->firma1 }} -
                                            {{ $activeBestellung->reAdresse->strasse }} -
                                            {{ $activeBestellung->reAdresse->plz }}
                                            {{ $activeBestellung->reAdresse->stadt }}</div>
                                    </div>
                                    <div class="flex flex-row">
                                        <div class="font-bold pr-2 text-sky-600  w-28">Lieferadr.:</div>
                                        <div class="truncate">{{ $activeBestellung->lfAdresse->firma1 }} -
                                            {{ $activeBestellung->lfAdresse->strasse }} -
                                            {{ $activeBestellung->lfAdresse->plz }}
                                            {{ $activeBestellung->lfAdresse->stadt }}</div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                </div>
                @if (!empty($activeBestellung))
                    @livewire('bestellung-position-component', ['bestellnr' => $activeBestellung->nr])
                @endif
            </div>
        </div>
    </div>

    <x-my-message :titel="$messageTitel" :hinweis="$messageHinweis"/>

</div>
