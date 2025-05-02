@props([
    'active' => false,
    'compact' => false, // kompakter Modus (z. B. für obere Links)
])

@php
    $baseClasses = 'inline-flex items-center border-b-4 transition duration-150 ease-in-out';
    $spacing = $compact
        ? 'px-1 py-[2px]'     // kompakter Modus (weniger Padding)
        : 'px-2 pt-1 pb-1';    // Standard-Modus (mehr Luft unten)

    $textStyle = 'text-sm font-medium leading-5';

    $colors = $active
        ? 'border-[#CDD503] dark:border-indigo-600 text-gray-900 dark:text-gray-100 focus:border-[#7c8023] border-b-2 border-sky-600 text-sky-600'
        : 'border-transparent dark:text-gray-400 hover:text-sky-600 dark:hover:text-gray-300 hover:border-sky-600 dark:hover:border-gray-700 focus:border-gray-300 dark:focus:border-gray-700';

    $classes = "$baseClasses $spacing $textStyle $colors";
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
