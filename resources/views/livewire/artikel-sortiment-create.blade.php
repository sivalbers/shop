<div>
    <form wire:submit.prevent="store">
        <div>
            <label for="artikelnr">Artikel-Nr.</label>
            <select wire:model="artikelnr" id="artikelnr">
                <option value="">Wähle einen Artikel</option>
                @foreach($artikels as $artikel)
                    <option value="{{ $artikel->artikelnr }}">{{ $artikel->bezeichnung }}</option>
                @endforeach
            </select>
            @error('artikelnr') <span>{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="sortiment">Sortiment</label>
            <select wire:model="sortiment" id="sortiment">
                <option value="">Wähle ein Sortiment</option>
                @foreach($sortimente as $sort)
                    <option value="{{ $sort->bezeichnung }}">{{ $sort->bezeichnung }}</option>
                @endforeach
            </select>
            @error('sortiment') <span>{{ $message }}</span> @enderror
        </div>
        <button type="submit">Speichern</button>
    </form>
</div>
