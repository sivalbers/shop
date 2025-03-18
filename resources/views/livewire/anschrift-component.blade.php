<div>
    <div class="w-full text-xs">
        <div class="border border-gray-500 w-4/5 rounded m-auto">
            <table class="table-auto w-full">
                <thead>
                    <tr class="bg-slate-300">
                        <th class="px-2 py-1 text-right">Kundennr.</th>
                        <th class="px-2 py-1 text-center">Std.</th>
                        <th class="px-2 py-1 text-center">Art</th>
                        <th class="px-2 py-1 text-left">Kurzbeschreibung</th>
                        <th class="px-2 py-1 text-left">Firma 1</th>
                        <th class="px-2 py-1 text-left">Firma 2</th>
                        <th class="px-2 py-1 text-left">Strasse</th>
                        <th class="px-2 py-1 text-left">PLZ-Ort</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-slate-300">
                        <td class="text-right"><input type="text" wire:model.lazy="kundennrFilter" class="suchFilter w-24" placeholder="(Suche)"></th>
                        <td>&nbsp;</th>
                        <td>
                            <select id="artFilter" wire:model.lazy="artFilter" class="suchFilter w-full">
                                <option value="">Alle</option>
                                <option value="Lieferadresse">LF-Adresse</option>
                                <option value="Rechnungsadresse">RE-Adresse</option>
                            </select>
                        </td>
                        <td class=""><input type="text" wire:model.lazy="kurzbeschreibungFilter" class="suchFilter w-full" placeholder="(Suche)"></td>
                        <td class=""><input type="text" wire:model.lazy="firma1Filter" class="suchFilter w-full" placeholder="(Suche)"></td>
                        <td class=""><input type="text" wire:model.lazy="firma2Filter" class="suchFilter w-full" placeholder="(Suche)"></td>
                        <td></td>
                        <td></td>
                    </tr>


                    @foreach ($anschriften as $anschrift)
                    <tr class="border-t">
                        <td class="px-2 py-1 text-right">{{ $anschrift->kundennr }}</td>
                        <td class="px-2 py-1 text-center">@if ($anschrift->standard == 1) J @else &nbsp; @endif</td>
                        <td class="px-2 py-1 text-center">
                            @if ($anschrift->art == 'Lieferadresse') LF-Adresse
                            @elseif($anschrift->art == 'Rechchnungsadresse') RE-Adresse
                            @else RE + LF
                            @endif

                        </td>
                        <td class="px-2 py-1">{{ $anschrift->kurzbeschreibung }}</td>
                        <td class="px-2 py-1">{{ $anschrift->firma1 }}</td>
                        <td class="px-2 py-1">{{ $anschrift->firma2 }}</td>
                        <td class="px-2 py-1">{{ $anschrift->strasse }}</td>
                        <td class="px-2 py-1">{{ $anschrift->plz }} - {{ $anschrift->stadt }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>



</div>
