<div>
    <div class="w-3/5 border border-red-500 m-auto p-1">
        <div class="text-gray-400 ml-2">
            Component: TestMaincomponent
        </div>
        <div class="flex flex-col w-full">
            <div>
                <div class="flex flex-row border border-blue-500">
                    <div class="flex flex-col border border-green-500 w-1/2 p-1"> <!-- Linke Spalte -->
                        <div class="border border-blue-400 p-1"> <!-- Spalte1 Zeile 1-->

                            <div class="flex flex-col border border-yellow-500 rounded p-1">



                                @livewire('TestMainUnterComponent')


                            </div>

                        </div>
                        <div class="border border-blue-400 p-1"> <!-- Spalte1 Zeile 2-->

                            <div class="flex flex-col border border-yellow-500 rounded p-1">

                                <div class="font-bold">
                                    Test-Main
                                </div>
                                <div>
                                    <form wire:submit.prevent="verarbeite">
                                        @csrf

                                        <div class="float float-col border border-pink-600 rounded p-1">
                                            <div class="float float-row">
                                                <label for="test">Testeingabe: </label>
                                                <input wire:model="testText" type="text" id="test">
                                            </div>

                                            <div class="float float-col text-right">
                                                <button type="button" wire:click="sendMainUnter"
                                                    class="border border-gray-400 rounded m-2 p-2">> unter</button>
                                                <button type="button" wire:click="sendMainRechts"
                                                    class="border border-gray-400 rounded m-2 p-2">> rechts</button>
                                                <button type="reset" wire:click="clear"
                                                    class="border border-gray-400 rounded m-2 p-2">Reset</button>
                                                <button type="submit"
                                                    class="border border-gray-400 rounded m-2 p-2">Senden</button>
                                            </div>
                                        </div>


                                    </form>
                                    @if ($isVerarbeitet)
                                        Text-Main: {{ $testText }}
                                    @endif
                                </div>

                            </div>

                        </div>
                    </div>
                    <div class=" border border-green-500 w-1/2 p-1"> <!-- Recht Spalte -->
                        <div class="flex flex-col border border-yellow-500 rounded p-1">
                            @livewire('TestMainRechtsComponent')
                        </div>
                    </div>
                </div>
            </div>
            <div class="border border-blue-500">

                @livewire('schnellerfassungComponent', [ 'sortiment' => 'EWE' ] )

            </div>
        </div>

    </div>
</div>
