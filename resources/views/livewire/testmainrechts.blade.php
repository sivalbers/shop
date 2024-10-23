<div>

    <div class="font-bold">
        Test-Main-Rechts
    </div>

    <div class="border border-violet-400 rounded p-1">
        <form wire:submit.prevent="verarbeite">
            @csrf

                <div class="float float-col border border-pink-600 rounded p-1">
                    <div class="float float-row">
                        <label for="test">Testeingabe: </label>
                        <input wire:model="testText" type="text" id="test">
                    </div>

                    <div class="float float-col text-right">
                        <button type="button" wire:click="sendMain" class="border border-gray-400 rounded m-2 p-2">> main</button>
                        <button type="button" wire:click="sendMainUnter" class="border border-gray-400 rounded m-2 p-2">> unter</button>

                        <button type="reset" wire:click="clear" class="border border-gray-400 rounded m-2 p-2">Reset</button>
                        <button type="submit" class="border border-gray-400 rounded m-2 p-2">Senden</button>
                    </div>
                </div>
        </form>
        @if ($isVerarbeitet)
            Text-Main-Rechts: {{ $testText }}
        @endif
    </div>
</div>
