<div>
    <div class="w-full text-xs">
        <div class="border border-gray-500 w-4/5 rounded m-auto">

            <div class="grid grid-cols-8 gap-x-4 gap-y-1 m-4">
                <div class="bg-slate-300 rounded p-3 font-bold">
                    Artikelnr.
                </div>
                <div class="col-span-3 bg-slate-300 rounded p-3 font-bold">
                    Bezeichnung
                </div>
                <div class="bg-slate-300 rounded p-3 font-bold">
                    Status
                </div>
                <div class="text-right bg-slate-300 rounded p-3 font-bold">
                    Preis
                </div>
                <div class="bg-slate-300 rounded p-3 font-bold">
                    Einheit
                </div>
                <div class="bg-slate-300 rounded p-3 font-bold">
                    WG<br>
                </div>

                <div class="bg-slate-300 rounded p-3 font-bold">
                    <input type="text" wire:model.lazy="artFilter" class="suchFilter w-full" placeholder="(Suche)">
                </div>
                <div class="col-span-3 bg-slate-300 rounded p-3 font-bold">
                    <input type="text" wire:model.lazy="bezFilter" class="suchFilter" placeholder="(Suche)">
                </div>
                <div class="bg-slate-300 rounded p-3 font-bold">
                    <select id="statusFilter" wire:model.lazy="statusFilter" class="suchFilter ">
                        <option value="">Alle</option>
                        <option value="aktiv">Aktiv</option>
                        <option value="gesperrt">Gesperrt</option>
                    </select>
                </div>
                <div class="text-right bg-slate-300 rounded p-3 font-bold">

                </div>
                <div class="bg-slate-300 rounded p-3 font-bold">

                </div>
                <div class="bg-slate-300 rounded p-3 font-bold ">
                    <input type="text" wire:model.lazy="wgFilter" class="w-full suchFilter" placeholder="(Suche)">
                </div>


                @foreach ($artikels as $artikel)
                    <div class="border px-1 py-1 @if ($artikel->status == 'gesperrt') line-through @endif">
                        {{ $artikel->artikelnr }}</div>
                    <div class="col-span-3 border px-1 py-1 @if ($artikel->status == 'gesperrt') line-through @endif">
                        <p class="relative group">
                            {{ $artikel->bezeichnung }}
                            @if (!empty(trim($artikel->langtext)))
                                <span
                                    class="absolute hidden group-hover:block bg-gray-700 text-white text-xs rounded py-1 px-2 w-64 -mt-8 z-10">
                                    {!! $artikel->langtext !!}
                                </span>
                            @endif
                        </p>
                    </div>
                    <div class="border px-1 py-1">{{ $artikel->status }}</div>
                    <div class="border px-1 py-1 text-right">{{ $artikel->vkpreis }} €</div>
                    <div class="border px-1 py-1">{{ $artikel->einheit }}</div>
                    <div class="border px-1 py-1">
                        <p class="relative group">
                            {{ $artikel->wgnr }}
                            <span
                                class="absolute hidden group-hover:block bg-gray-700 text-white text-xs rounded py-1 px-2 w-64 -mt-8 z-10">
                                {!! $artikel->warengruppe->bezeichnung !!}
                            </span>
                        </p>
                    </div>

                @endforeach

                <div class="col-span-7">
                    {{ $artikels->links() }}<br>

                </div>

            </div>
        </div>
    </div>
</div>
