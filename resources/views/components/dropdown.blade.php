@props(['align' => 'right', 'width' => '56', 'contentClasses' => 'py-1 bg-white dark:bg-gray-700', 'content' => ''])

@php
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    default => 'ltr:origin-top-right rtl:origin-top-left end-0',
};
$width = '';
/*
$width = match ($width) {

    '56' => 'w-80',
    '96' => 'w-96',
    default => $width,
};
*/
@endphp


<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div  @mouseenter="open = true" @mouseleave="open = false">
        {{ $trigger }}


        <div x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute z-50 ml-20 -mt-2  {{ $width }} rounded-md shadow-lg {{ $alignmentClasses }}"
                style="display: none;"
                @click="open = false">
            <div class="rounded-md ring-1 ring-black ring-opacity-5 border border-gray-600 {{ $contentClasses }}">
                {{ $content }}
            </div>
        </div>
    </div>
</div>
