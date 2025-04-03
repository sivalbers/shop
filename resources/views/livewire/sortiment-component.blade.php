<div x-data="{ showMessage: false, showEditWindow: @entangle('showEditWindow') }"
        x-cloak>


    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <div class="text-sm w-4/5 m-auto">
        <div class="flatwhite">
            <div class="flex flex-col items-center text-base">
                <div class="flex flex-row w-full justify-between text-sky-600 text-xl font-bold px-2 py-4">
                    <div>
                    Sortimente
                    </div>
                    <div>
                    <button title="Neues Sortiment anlegen" class="text-ewe-gruen flex items-center" type="button"
                        wire:click="create">
                        <x-fluentui-receipt-add-24-o class="h-10" />
                    </button>
                    </div>
                </div>

                <div class="flex flex-row w-full text-sky-600 font-bold border-b border-sky-600">
                    <div class="w-1/3 px-2 ">
                        Bezeichnung
                    </div>
                    <div class="w-1/3 px-2 ">
                        Anzeigename
                    </div>
                    <div class="w-1/3 px-2 ">
                        Aktion
                    </div>
                </div>

                @foreach ($sortimente as $sortiment)
                    <div class="flex flex-row items-center w-full hover:bg-ewe-ltgruen">
                        <div class="w-1/3 px-2 py-1">{{ $sortiment->bezeichnung }}</div>
                        <div class="w-1/3 px-2 py-1">{{ $sortiment->anzeigename }}</div>
                        <div class="w-1/3 px-2 py-1  text-sm">
                            <button wire:click="edit('{{ $sortiment->bezeichnung }}')">Bearbeiten</button> |
                            <button wire:click="delete('{{ $sortiment->bezeichnung }}')">Löschen</button>
                        </div>
                    </div>
                @endforeach
                <div class="h-12 w-full border-t border-sky-600">

                    <div x-data="{ show: false }" x-init="@this.on('status-updated', () => { show = true; setTimeout(() => show = false, 3000) })">
                        @if ($statusMessage)
                            <div x-show="show" x-transition class="bg-ewe-ltgruen text-sky-600 px-4 py-2 rounded">
                                {{ $statusMessage }}
                            </div>
                        @endif
                    </div>


                </div>
            </div>
        </div>

        <div x-show="showEditWindow">
        @include('livewire.sortiment-edit')
        </div>

    </div>


</div>
