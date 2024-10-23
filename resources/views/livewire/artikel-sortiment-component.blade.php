<div>
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    @if($updateMode)
        @include('livewire.artikel-sortiment-edit')
    @else
        @include('livewire.artikel-sortiment-create')
    @endif

    <table class="table-fixed w-full">
        <thead>
            <tr>
                <th class="px-4 py-2">Artikel-Nr.</th>
                <th class="px-4 py-2">Sortiment</th>
                <th class="px-4 py-2">Aktionen</th>
            </tr>
        </thead>
        <tbody>
            @foreach($artikelSortimente as $artikelSortiment)
            <tr>
                <td class="border px-4 py-2">{{ $artikelSortiment->artikelnr }}</td>
                <td class="border px-4 py-2">{{ $artikelSortiment->sortiment }}</td>
                <td class="border px-4 py-2">
                    <button wire:click="edit('{{ $artikelSortiment->artikelnr }}', '{{ $artikelSortiment->sortiment }}')">Bearbeiten</button>
                    <button wire:click="delete('{{ $artikelSortiment->artikelnr }}', '{{ $artikelSortiment->sortiment }}')">Löschen</button>
                </td>
            </tr>
            @endforeach
            <tr>
                <td colspan="4">
                    {{ $artikelSortimente->links() }}

                </td>
            </tr>
        </tbody>
    </table>
</div>
