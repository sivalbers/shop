<div>
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif



    <div class="w-full text-sm">
        <div class="border border-gray-500 w-4/5 rounded m-auto">
            <div class="grid grid-cols-4 gap-x-4 gap-y-1 m-4">
                <div class="bg-slate-300 rounded p-3 font-bold">
                    Warengruppe
                </div>
                <div class="col-span-3 bg-slate-300 rounded p-3 font-bold">
                    Bezeichnung
                </div>

                <div class="bg-slate-300 rounded p-3 font-bold">
                    <input type="text" wire:model.live="wgFilter" class="suchFilter" placeholder="(Suche)">
                </div>
                <div class="col-span-3 bg-slate-300 rounded p-3 font-bold">
                    <input type="text" wire:model.live="bezFilter" class="suchFilter" placeholder="(Suche)">
                </div>


                @foreach ($warengruppen as $warengruppe)
                    <div class="border px-1 py-1">{{ $warengruppe->wgnr }}</div>
                    <div class="col-span-3 border px-1 py-1">{{ $warengruppe->bezeichnung }}</div>
                @endforeach
                <div class="col-span-3">
                    {{ $warengruppen->links() }}
                </div>
            </div>
        </div>
    <!--
        @php

    @endphp
    -->
</div>
