
<div>
    <div class="flex flex-col w-full overflow-y-hidden" id="top" x-data="{}"
    x-on:click.self="zeigeFavoritPosForm = false;"
    x-on:keydown.escape.window="zeigeFavoritPosForm = false;">
   <div class="text-sm text-sky-600">
       <div class="flex flex-row items-center w-full">
           <div class="text-base font-bold border-b border-sky-600 w-full h-6 truncate">
               @php
                   use App\Enums\Tab;
               @endphp

               @if ($selectedTab === Tab::arWG)
                   Artikel
                   @if (!empty($selectedWarengruppeBezeichung) && $selectedWarengruppeBezeichung != '')
                       der Warengruppe "{{ $selectedWarengruppeBezeichung }}"
                   @endif
               @elseif ($selectedTab === Tab::arSchnellerfassung)
                   Artikel der Schnellerfassung aus dem Kundensortiment
               @elseif ($selectedTab === Tab::arSuche)
                   Artikelsuche
                   @if ($anzGefunden < 200)
                       <span class="text-xs">({{ $anzGefunden }} gefunden)</span>
                   @else
                       <span class="text-red-500">( Die Suche wurde auf 200 Treffer beschränkt )</span>
                   @endif
               @elseif ($selectedTab === Tab::arFavoriten)
                   Favoritenliste
                   @if ($anzGefunden < 200)
                       <span class="text-xs">({{ $anzGefunden }} gefunden)</span>
                   @else
                       <span class="text-red-500">( Die Suche wurde auf 200 Treffer beschränkt )</span>
                   @endif
               @endif
           </div>
           <div class="ml-auto flex flex-row">
               <a href="#" wire:click="toggle_listKurz" title="Kurzliste">
                   <div
                       class="border border-sky-600 rounded ml-2 mr-2 @if ($listKurz) bg-sky-600 text-white @endif">
                       <x-fluentui-apps-list-20-o class="h-6" />
                   </div>
               </a>
               <a href="#" wire:click="toggle_listKurz" title="Detailliert">
                   <div
                       class="border border-sky-600 rounded @if (!$listKurz) bg-sky-600 text-white @endif">
                       <x-fluentui-apps-list-detail-20-o class="h-6" />
                   </div>
               </a>
           </div>
       </div>
   </div>

   <!-- Scrollbarer Bereich -->
   <div class="relative w-full overflow-y-auto h-[30vh] md:h-[calc(100vh-245px)] scrollbar-hide">
       <form wire:submit.prevent="InBasket">
           @csrf




           @foreach ($aPositions as $index => $position)
                <div wire:key="pos-{{ $position['uid'] }}">
                    @if (!$listKurz)
                        <x-artikel-detail :pos="$position" :loop="$loop" />
                    @else
                        <x-artikel-kurz :pos="$position" :loop="$loop" />
                    @endif
                </div>
            @endforeach


           <!-- Scroll-to-Top Button -->
           <button onclick="scrollToTop()" type="button"
               class="fixed bottom-1 right-[50%] z-10 border rounded-3xl border-sky-600 bg-sky-600 text-white p-1 opacity-50 hover:opacity-100"
               title="nach oben scrollen"><x-fluentui-arrow-upload-16 class="h-6" /></button>


            <!-- Button Container -->
            <div class="fixed bottom-0 right-[5%] flex space-x-4 z-10 mb-1 opacity-100">
                <button type="submit" class="w-52 bg-sky-600 text-white py-2 rounded-md hover:font-bold shadow-md">
                    In den Warenkorb
                </button>
            </div>
        </form>

        </div>
</div>

<script>
   function scrollToTop() {
       let element = document.querySelector('.overflow-y-auto'); // Nur den scrollbaren Bereich auswählen
       if (element) {
           element.scrollTo({
               top: 0,
               behavior: 'smooth'
           });
       }
   }
</script>


</div>
