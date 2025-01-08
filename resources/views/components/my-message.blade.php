<div class="flex fixed top-0 bg-opacity-60 item-center w-full h-full bg-slate-100 backdrop-blur-[2px]"
    x-show="zeigeMessage" x-cloak x-on:click.self="zeigeMessage = false"
    x-on:keydown.escape.window="zeigeMessage = false"> <!-- gesamtes Fenster backdrop-blur-[2px] -->

    <div class="w-6/12 m-auto  text-center bg-opacity-0" x-data="">
        <!-- Abfragefenster Fenster -->

        <div class="m-4 p-4 border rounded-md border-sky-600 bg-sky-600 shadow-md">


            <div class="flex flex-row items-center w-full">
                <div class="flex flex-col w-5/6">
                    <div class="text-3xl font-bold items-center text-white">
                        Bestellung wurden gespeichert!
                        {{ $slot }}
                    </div>
                    <div class="text-xl  items-center text-white">
                        Ihre Bestellbestätigung erhalten Sie in Kürze per Mail.

                    </div>
                </div>
                <div class="text-right  w-1/6">
                    <button x-on:click="zeigeMessage = false" class="py-2 px-4 border border-gray-400 bg_ewe_gruen rounded-md">
                        Schliessen
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
