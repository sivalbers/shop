<div>
    <div class="flex flex-col">
        <button wire:click="import('Artikel')">Importiere Artikel</button>
        <button wire:click="import('WG')">Importiere Warengruppen</button>
        <button wire:click="import('Sortiment')">Importiere Sortiment</button>
        <button wire:click="import('Favoriten')">Importiere Favoriten</button>
    </div>

    <div class="my-4">
        <div class="w-full bg-gray-300 h-6 rounded">
            <div class="bg-blue-500 h-6 rounded" style="width: {{ $progress }}%"></div>
        </div>
        <p class="text-sm">Fortschritt: {{ $progress }}%</p>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    @if ($fehlendeDebitoren)
        <div class="text-yellow-600">
            <p>Fehlende Debitoren:</p>
            <ul class="list-disc ml-5">
                @foreach ($fehlendeDebitoren as $debitor)
                    <li>{{ $debitor }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($fehlerhafte)
        <div class="text-red-600 mt-4">
            <p>Fehlerhafte Zeilen:</p>
            <ul class="list-disc ml-5 text-sm">
                @foreach ($fehlerhafte as $fehler)
                    <li>Zeile {{ $fehler['zeile'] }} (Artikel {{ $fehler['artikelNr'] }}): {{ $fehler['fehler'] ?? 'unbekannter Fehler' }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
