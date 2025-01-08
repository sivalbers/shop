<div>
    <div class="flex flex-col w-full">
        <div class="text-sm text-sky-600 ">
            <div class="flex flex-row items-center w-full">
                <div class="text-base font-bold border-b border-sky-600 w-full h-6"> <!-- Höhe okay -->

                    Artikel im Warenkorb

                </div>
            </div>
        </div>

        <form wire:submit.prevent="BtnSpeichern" class="">
            @csrf

            <table class="table-auto w-full ">
                <thead>
                    <tr class="border-b border-gray-300">
                        <th class="px-4 py-2 text-right" colspan="3">Vorraussichtlicher Gesamtbetrag *:</th>
                        <th class="px-4 py-2  text-right" colspan="2">
                            {{ number_format($bestellung->gesamtbetrag, 2, ',', '.') }} €</th>
                        <th class="px-4 py-2">&nbsp;</th>
                        <th class="px-4 py-2">&nbsp;</th>
                    </tr>

                    <tr class="bg-gray-100 border-b border-gray-300">

                        <th class="px-4 py-2 text-left">Artikel - Beschreibung</th>
                        <th class="px-4 py-2 ">Menge</th>
                        <th class="px-4 py-2 ">Einh.</th>
                        <th class="px-4 py-2  text-right">E-Preise €</th>
                        <th class="px-4 py-2  text-right">G-Preise €</th>
                        <th class="px-4 py-2">&nbsp;</th>
                        <th class="px-1 py-1 ">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        //Log::info('in ShopPosition.blade.php');
                        //     dd($bPositionen);
                    @endphp
                    @foreach ($bPositionen as $key => $position)
                        <tr class="border-b border-gray-300">

                            <!-- Artikel -->
                            <td class="px-4 py-1 ">
                                <div class="flex flex-row items-center">
                                    <span class="relative group text-gray-500 pr-2">
                                        <x-fluentui-info-12-o class="w-5" />
                                        @if (!empty(trim($position['langtext'])))
                                            <span
                                                class="absolute hidden group-hover:block bg-gray-700 text-white text-xs rounded py-1 px-2 w-64 -mt-8 z-10">
                                                {!! $position['langtext'] !!}
                                            </span>
                                        @endif
                                    </span>
                                    <a href="javascript:void(0)"
                                        wire:click.prevent="$dispatch('showArtikel', { artikelnr: '{{ $position['artikelnr'] }}' })"
                                        class="hover:underline">
                                        {{ $position['artikelnr'] }} - {{ $position['bezeichnung'] }}
                                    </a>
                                </div>
                            </td>
                            <!-- Menge -->


                            <td class="px-0 py-1 text-center">
                                <div x-data="{ quantity: @js($bPositionen[$key]['menge']) }"
                                     @basket-cleared.window="quantity = 0"
                                     @positionsreloaded.window="quantity = @js($bPositionen[$key]['menge'])"
                                     x-init="
                                        $watch('quantity', value => {
                                            $wire.set('bPositionen.{{ $key }}.menge', value);
                                        });

                                        // Beobachte Änderungen von Livewire und aktualisiere quantity
                                        Livewire.hook('message.processed', (message, component) => {
                                            quantity = @js($bPositionen[$key]['menge']);
                                        });
                                     "
                                     class="flex items-center overflow-hidden border border-gray-400 rounded">

                                    <!-- Button zum Verringern der Menge -->
                                    <button type="button"
                                            @click="quantity = Math.max(0, quantity - 1)"
                                            class="flex-1 bg-gray-100 text-gray-700 py-0.5 hover:bg-blue-200 h-7 w-7 border-r border-r-gray-400">
                                        -
                                    </button>

                                    <!-- Eingabefeld zur direkten Bearbeitung der Menge -->
                                    <input type="text"
                                           x-model="quantity"
                                           wire:model.lazy="bPositionen.{{ $key }}.menge"
                                           class="px-1 w-14 text-center border-none outline-none text-xs h-7">

                                    <!-- Button zum Erhöhen der Menge -->
                                    <button type="button"
                                            @click="quantity++"
                                            class="flex-1 bg-gray-100 text-gray-700 py-0.5 hover:bg-blue-200 h-7 w-7 border-l border-l-gray-400">
                                        +
                                    </button>

                                </div>
                                @error('bPositionen.{{ $key }}.menge')
                                    <div class="text-2xl">!</div>
                                @enderror
                            </td>

                            <!-- Einheit -->
                            <td class="px-4 py-1 text-center">{{ $position['einheit'] }}</td>
                            <!-- E-Preise -->
                            <td class="px-4 py-1 text-right">{{  formatPreis($position['epreis']) }}</td>
                            <!-- G-Preise -->
                            <td class="px-4 py-1 text-right">
                                {{ formatGPreis($position['gpreis']) }}</td>
                            <!-- VV -->
                            <td class="w-auto px-1 py-1 text-center">
                                @if ($bPositionen[$key]['bestand'] === 0)
                                    <x-fluentui-vehicle-truck-profile-24-o title="aktuell nicht auf Lager"
                                        class="h-7 text-red-500" />
                                @else
                                    <x-fluentui-vehicle-truck-profile-24 title="wahrscheinlich lieferbar"
                                        class="h-7 text-[#CDD503]" />
                                @endif
                            </td>
                            <td class="px-2 py-2 "><a href="#"
                                    wire:click="btnDelete({{ $key }})" class="hover:text-red-500"
                                    title="direkt löschen"> <x-fluentui-delete-28-o class="h-5" /></a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div>* Der endgültige Preis kann abweichen.</div>


            <div class="relative w-11/12 h-screen overflow-y-auto">
                <!-- Container für die beiden Buttons -->
                <div class="fixed bottom-0 right-[5%] flex space-x-4 z-10 mb-1 ">
                    <div class="flex flex-row">
                        <div class="mr-2">




                            @if ($isPosModified)
                                <button type="button" wire:click="BtnCancel"
                                    class="w-52  bg-[#CDD503] text-sky-600 py-2 rounded-md"
                                    @if (!$isPosModified) disabled @endif">

                                    Abbrechen
                                </button>
                            @endif
                        </div>
                        @php
                            $farbe = 'bg-gray-500 text-white';
                            if (count($bPositionen) > 0){
                                if ($isPosModified)
                                    { $farbe = 'bg-sky-600 text-white'; }
                                else
                                    { $farbe = 'font-bold bg_ewe_gruen text-sky-600'; }
                            }
                        @endphp
                        <div class="">
                            <button type="submit" class="w-52 {{ $farbe }} py-2 rounded-md">

                                @if (!$isPosModified)
                                    @if (count($bPositionen) > 0 )
                                        Bestellung absenden
                                    @else
                                        Keine Änderungen
                                    @endif
                                @else
                                    Änderungen speichern
                                @endif
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script>
        document.addEventListener('refresh-page', function () {
            window.location.reload();
        });
    </script>
</div>
