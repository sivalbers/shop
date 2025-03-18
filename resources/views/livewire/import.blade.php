<div>
    <div class="flex flex-col">
        <button wire:click="import('Artikel')">Importiere Artikel</button>
        <button wire:click="import('WG')">Importiere Warengruppen</button>
        <button wire:click="import('Sortiment')">Importiere Sortiment</button>
    </div>
    <div class="flex flex-col">

        @if (session()->has('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif
    </div>
</div>
