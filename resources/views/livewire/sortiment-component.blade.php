<div>


    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif






    <div class="w-full text-sm">
        <div class="border border-gray-500 w-4/5 rounded m-auto" >
            <div class="grid grid-cols-3 gap-x-4 gap-y-1 m-4">
                <div class="col-span-2 bg-slate-300 rounded p-3 font-bold">
                    Bezeichnung
                </div>
                <div class="bg-slate-300 rounded p-3 font-bold">
                    Aktion
                </div>


                @foreach($sortimente as $sortiment)



                    <div class="col-span-2 border px-1 py-1">{{ $sortiment->bezeichnung }}</div>
                    <div class="border px-1 py-1">
                        <button wire:click="edit('{{ $sortiment->bezeichnung }}')">Bearbeiten</button>
                        <button wire:click="delete('{{ $sortiment->bezeichnung }}')">Löschen</button>
                    </div>

                @endforeach
            </div>
        </div>

        <div class="w-2/3 m-auto">
            @if(!is_null($updateMode) && $updateMode)
                @include('livewire.sortiment-edit')
            @else
                @include('livewire.sortiment-create')
            @endif
        </div>

    <div x-data="{ show: false }" x-init="@this.on('status-updated', () => { show = true; setTimeout(() => show = false, 3000) })">
        @if ($statusMessage)
            <div x-show="show" x-transition class="bg-green-500 text-white p-2 rounded">
                {{ $statusMessage }}
            </div>
            {{ $statusMessage }}
        @endif
    </div>
</div>
