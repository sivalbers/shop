<div x-data="{ fopen: false }">
    <div class="hidden md:flex flex-row">
        <div class="flex-1 m-4"><a href="{{ route('datenschutz') }}">Datenschutz</a></div>
        <div class="flex-1 m-4"><a href="{{ route('impressum') }}">{{ trans('auth.Impressum') }}</a></div>
    </div>

    <div class="md:hidden -me-2 items-center ">
        <button @click="fopen = ! fopen"
            class="border border-gray-300 inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
            <svg class="h-4 w-4" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path :class="{ 'hidden': fopen, 'inline-flex': !fopen }" class="inline-flex" stroke-linecap="round"
                    stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path :class="{ 'hidden': !fopen, 'inline-flex': fopen }" class="hidden" stroke-linecap="round"
                    stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>

        </button>
    </div>

    <div :class="{ 'block': fopen, 'hidden': !fopen }" class="sm:float hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('datenschutz')" :active="request()->routeIs('datenschutz')" wire:navigate>
                {{ __('Datenschutz') }}
            </x-responsive-nav-link>


            <x-responsive-nav-link :href="route('impressum')" :active="request()->routeIs('impressum')" wire:navigate>
                {{ __('Impressum') }}
            </x-responsive-nav-link>
        </div>
    </div>

</div>
