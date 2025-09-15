@props(['images', 'size', 'artikelnr', 'beschreibung'])

@if (!empty($images))
    <!-- Erstes Bild sichtbar -->
    <a href="{{ asset('storage/products_big/' . $images[0]) }}"
       data-lightbox="galerie-{{ $artikelnr }}"
       data-title="{{ $artikelnr }} - {{ $beschreibung }}">
        <img class="border-2 border-slate-400 rounded-md"
             style="width: {{ $size }}px;"
             src="{{ asset('storage/products_big/' . $images[0]) }}"
             alt="Produktbild">
    </a>

    <!-- Weitere Bilder versteckt für Lightbox -->
    @foreach(array_slice($images, 1) as $bild)
        <a href="{{ asset('storage/products_big/' . $bild) }}"
           data-lightbox="galerie-{{ $artikelnr }}"
           data-title="{{ $artikelnr }} - {{ $beschreibung }}"
           style="display: none;"></a>
    @endforeach
@else
    <img class="border-2 border-slate-400 rounded-md"
         style="width: {{ $size }}px;"
         src="{{ asset('storage/products_big/blank.png') }}"
         alt="Kein Bild verfügbar">
@endif
