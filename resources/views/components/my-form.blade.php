
    <!--  Fensterfarbe ursprünglich border-2 border-blue-100 bg-blue-200 shadow-slate-600  ring-4 ring-blue-200 rounded-md shadow-2xl -->
    <div class="flex fixed left-0 top-0 items-center w-full h-full bg-slate-100/60 backdrop-blur-[2px]"

        x-show="showForm" x-cloak
        x-on:click.self="showForm = false"
        x-on:keydown.escape.window="showForm = false"> <!-- gesamtes Fenster backdrop-blur-[2px] -->

        <div {{ $attributes->merge(['class' => 'w-8/12 m-auto  flatwhite']) }}

            x-data="{ isDisabled: true }"
            x-init="$watch('$wire.isModified', value => isDisabled = false);"> <!-- Abfragefenster Fenster -->

            <div class="m-2">
                {{ $slot->isEmpty() ? 'Saved.' : $slot }}
            </div>
        </div>
    </div>
