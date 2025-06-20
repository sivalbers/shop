@props(['titel', 'hinweis', 'dauer' => 5000]) {{-- Dauer in Millisekunden, Standard: 5000ms (5 Sekunden) --}}

<div class="fixed inset-0 flex items-center justify-center w-full h-full bg-slate-100/60 backdrop-blur-[2px]"
     x-show="zeigeMessage"
     x-cloak
     x-on:click.self="zeigeMessage = false"
     x-on:keydown.escape.window="zeigeMessage = false"
     x-data="{ progress: 0, interval: null }"
     x-effect="
        if (zeigeMessage) {
            progress = 0;
            clearInterval(interval);
            interval = setInterval(() => {
                progress += 100 / ({{ $dauer }} / 100);
                if (progress >= 100) {
                    clearInterval(interval);
                    zeigeMessage = false;
                }
            }, 100);
        } else {
            progress = 0;
            clearInterval(interval);
        }
     ">

    <!-- Dialog -->
    <div class="w-6/12 m-auto text-center ">
        <div class="m-4 p-4 border rounded-md border-sky-600 bg-sky-600 shadow-md">

            <div class="flex flex-row items-center w-full">
                <div class="flex flex-col w-5/6">
                    <div class="text-3xl font-bold text-white">
                        {{ $titel ?? 'Bestellung wurde gespeichert!' }}
                        {{ $slot }}
                    </div>
                    <div class="text-xl text-white">
                        {{ $hinweis ?? 'Ihre Bestellbestätigung erhalten Sie in Kürze per E-Mail.' }}
                    </div>
                </div>
                <div class="w-1/6 text-right">
                    <button x-on:click="zeigeMessage = false" class="py-2 px-4 border border-gray-400 bg-ewe-gruen rounded-md">
                        Schließen
                    </button>
                </div>
            </div>

            <!-- Fortschrittsbalken -->
            <div class="mt-4 h-2 bg-gray-300 rounded-full overflow-hidden">
                <div class="h-full bg-ewe-gruen transition-all ease-linear"
                     :style="{ width: progress + '%' }"></div>
            </div>

        </div>
    </div>
</div>
