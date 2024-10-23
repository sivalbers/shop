<div>




    <div class="flex flex-col w-full " id="top">
        <div class="text-sm text-sky-600 ">
            <div class="flex flex-row items-center w-full">
                <div class="text-base font-bold border-b border-sky-600 w-full h-6 truncate ...">  <!-- Höhe okay -->

                    @php
                        use App\Enums\Tab;
                    @endphp

                    @if ($selectedTab === Tab::arWG)
                    Artikel @if (!empty($selectedWarengruppeBezeichung) && $selectedWarengruppeBezeichung != '') der Warengruppe "{{ $selectedWarengruppeBezeichung }}" @endif


                    @elseif ($selectedTab === Tab::arSchnellerfassung)
                        Artikel der Schnellerfassung aus dem Kundensortiment
                    @else
                        SOnstiges
                    @endif
                </div>
                <div class="ml-auto flex flex-row">
                    <a href="#" wire:click="toggle_listKurz" title="Kurzliste"><div class="border border-sky-600 rounded ml-2 mr-2 @if ($listKurz) bg-sky-600 text-white @endif"> <x-fluentui-apps-list-20-o  class="h-6"/> </div></a>
                    <a href="#" wire:click="toggle_listKurz" title="Detailliert"><div class="border border-sky-600 rounded @if (!$listKurz) bg-sky-600 text-white @endif"> <x-fluentui-apps-list-detail-20-o class="h-6"/> </div></a>

                </div>
            </div>
        </div>

        <form wire:submit.prevent="InBasket" class="overflow-scroll">
            @csrf

            @foreach ($artikels as $artikel)
                @if (!$listKurz)

                    <x-artikel-detail :artikel="$artikel" :quantities="$quantities" />
                @else
                    <x-artikel-kurz :artikel="$artikel" :quantities="$quantities" />

                @endif
            @endforeach
            <div class="relative w-4/5 h-screen overflow-y-auto">
            <button onclick="scrollToTop()" type="button" class="fixed bottom-1 right-[50%] z-10 border rounded-3xl border-sky-600 bg-sky-600 text-white p-1 opacity-50 hover:opacity-100" title="nach oben scrollen"><x-fluentui-arrow-upload-16 class="h-6"/></button>
            </div>
            <div class="relative w-4/5 h-screen overflow-y-auto">
                <!-- Container für die beiden Buttons -->
                <div class="fixed bottom-0 right-[5%] flex space-x-4 z-10 mb-1 opacity-50 hover:opacity-100">
                    <button type="submit" class="w-52 bg-sky-600 text-white py-2 rounded-md">
                        In den Warenkorb
                    </button>
                </div>
            </div>
        </form>

    </div>
    <script>
        function scrollToTop() {
            let element = document.querySelector('#tab5');


            if (element) {
                // element.scrollTop = 0;
                element.scrollTo({
                top: 0,
                behavior: 'smooth' // sanftes Scrollen
            });

            }

            // Scrolle die Hauptseite nach oben
            window.scrollTo({
                top: 0,
                behavior: 'smooth' // sanftes Scrollen
            });

        }
    </script>

</div>
