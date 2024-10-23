<div>
    <div class="w-full text-xs">
        <div class="border border-gray-500 w-4/5 rounded m-auto">

            <table class="table-auto w-fullborder">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2">Nr</th> <!-- Laufende Nummer -->
                        <th class="px-4 py-2">Datum</th>
                        <th class="px-4 py-2">Kundennr</th>
                        <th class="px-4 py-2">Besteller</th>
                        <th class="px-4 py-2">Rechnungsanschrift</th>
                        <th class="px-4 py-2">Lieferanschrift</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Kundenbestellnr</th>
                        <th class="px-4 py-2">Kommission</th>
                        <th class="px-4 py-2">Gesamtbetrag</th>
                        <th class="px-4 py-2">Lieferdatum</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    </tr>
                    @foreach ($bestellungen as $bestellung)
                        <tr class="cursor-pointer h-2 py-2 {{ $activeBestellung === $bestellung->nr ? 'bg-blue-300' : 'hover:bg-blue-200 ' }}"
                            wire:click="loadPositionen('{{ $bestellung->nr }}')">
                            <td class="border px-4">{{ $bestellung->nr }}</td>
                            <td class="border px-4">{{ $bestellung->datum }}</td>
                            <td class="border px-41">{{ $bestellung->kundennr }}</td>
                            <td class="border px-4">{{ $bestellung->besteller }}</td>
                            <td class="border px-4">{{ $bestellung->rechnungsadresse ?? 'N/A' }}</td>
                            <td class="border px-4">{{ $bestellung->lieferadresse ?? 'N/A' }}</td>
                            <td class="border px-4">{{ $bestellung->status_bezeichnung }}</td>
                            <td class="border px-4">{{ $bestellung->kundenbestellnr }}</td>
                            <td class="border px-4">{{ $bestellung->kommission }}</td>
                            <td class="border px-4 text-right">{{ number_format($bestellung->gesamtbetrag, 2, ',', '.') }} €</td>
                            <td class="border px-4">{{ $bestellung->lieferdatum }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Anzeige der ausgewählten Bestellung -->
            <div class="text-2xl text-red-500 mt-4 ml-4">Aktive Bestellung: {{ $activeBestellung }}</div>

            <!-- Bestellungspositionen anzeigen -->
            @livewire('bestellung-position-component')
        </div>
    </div>
</div>
