@props(['image', 'size'])
<img class="border-2 border-slate-400 rounded-md " style="width: {{ $size }}px; height: {{ $size }}px;" src="{{ asset('storage/products/' . $image) }}" alt="Produktbild">
