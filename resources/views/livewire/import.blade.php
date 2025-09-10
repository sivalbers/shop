<div class="w-10/12 m-auto">
    <div class="flex flex-col">
        <button wire:click="import('Artikel')" class="cursor-pointer  hover:bg-ewe-ltgruen">Importiere Artikel</button>
        <button wire:click="import('ArtikelBestand')" class="cursor-pointer hover:bg-ewe-ltgruen">Importiere Artikelbestände</button>
        <button wire:click="import('WG')" class="cursor-pointer hover:bg-ewe-ltgruen">Importiere Warengruppen</button>
        <button wire:click="import('Sortiment')" class="cursor-pointer hover:bg-ewe-ltgruen">Importiere Sortiment</button>
        <button wire:click="import('Favoriten')" class="cursor-pointer hover:bg-ewe-ltgruen">Importiere Favoriten</button>

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
