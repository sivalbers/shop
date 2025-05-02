@if (auth()->user() && auth()->user()->isAdmin())

<!-- Artikel Links -->
<x-dropdown align="right" width="48" class="">
    <x-slot name="trigger">
        <button
            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
            <div>Stammdaten</div> <!-- /*  ###########################  */ -->

            <div class="ms-1">
                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
            </div>
        </button>
    </x-slot>

    <x-slot name="content">
        <x-dropdown-link :href="route('artikel')" wire:navigate>
            {{ __('Artikel') }} <!-- /*  ###########################  */ -->
        </x-dropdown-link>

        <x-dropdown-link :href="route('sortimente')" wire:navigate>
            {{ __('Sortimente') }} <!-- /*  ###########################  */ -->
        </x-dropdown-link>
        <x-dropdown-link :href="route('warengruppen')" wire:navigate>
            {{ __('Warengruppen') }} <!-- /*  ###########################  */ -->
        </x-dropdown-link>
        <hr>
        <x-dropdown-link :href="route('anschriften')" wire:navigate>
            {{ __('Kunden - Anschriften') }} <!-- /*  ###########################  */ -->
        </x-dropdown-link>
    </x-slot>
</x-dropdown>

<x-dropdown align="right" width="48">
    <x-slot name="trigger">
        <button
            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
            <div>Import</div> <!-- /*  ###########################  */ -->

            <div class="ms-1">
                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
            </div>
        </button>
    </x-slot>

    <x-slot name="content">
        <x-dropdown-link :href="route('import')" :active="request()->routeIs('import')" wire:navigate>
            {{ __('Import') }} <!-- /*  ###########################  */ -->
        </x-dropdown-link>

        <x-dropdown-link :href="route('importArtikel')" :active="request()->routeIs('importArtikel')" wire:navigate>
            {{ __('Import Artikel') }} <!-- /*  ###########################  */ -->
        </x-dropdown-link>


        <x-dropdown-link :href="route('importWG')" :active="request()->routeIs('importWG')" wire:navigate>
            {{ __('Import Warengruppe') }} <!-- /*  ###########################  */ -->
        </x-dropdown-link>


        <x-dropdown-link :href="route('importSortiment')" :active="request()->routeIs('importSortiment')" wire:navigate>
            {{ __('Import Sortiment') }} <!-- /*  ###########################  */ -->
        </x-dropdown-link>
    </x-slot>
</x-dropdown>
@endif
