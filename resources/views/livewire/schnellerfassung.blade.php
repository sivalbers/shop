<div>
    <form wire:submit.prevent="verarbeiteText">
        @csrf
        <div x-data="{
            handleKeydown(event) {
                if (event.ctrlKey && event.key === 'Enter') {
                    $refs.submitButton.click();
                }
            }
        }"
        x-init="$el.addEventListener('keydown', handleKeydown)"
        >
        <div class="text-sm">
            Sie haben hier die Möglichkeit Ihre Artikel mit Menge direkt über die Zwischenablage einzufügen.
            <br>
            Die Spalten müssen durch Tabulatoren, Semikolon oder Komma getrennt werden.

        </div>
        <textarea wire:model="inText" class="w-full h-32 border rounded-md p-2"></textarea>
        <div class="flex justify-end">
        <button type="submit" x-ref="submitButton" class="mt-2 px-4 py-2 bg-sky-600 text-white rounded-md">Verarbeiten</button>
        </div>

    </div>



    </form>

    @if ($verarbeitet)
        <p class="mt-4">Verarbeitungsergebnis*: </p>
        <div class="w-full">
        <div class="flex flex-col">
            <div class="flex flex-row ml-2">
                <div class="w-52 border-b border-b-slate-500">Artikelnummer</div>
                <div class="w-28 border-b border-b-slate-500 text-right">Menge</div>
            </div>
            @foreach ($artikel as $art)
                <div class="flex flex-row ml-2">
                    <div class="w-52">{{ $art['artikelnummer'] }}</div>
                    <div class="w-28 text-right"> {{ $art['menge'] }}</div>
                </div>

            @endforeach
        </div>
        <span class="text-xs">*vor Sortimentsprüfung</span>
    </div>
    @endif
</div>
