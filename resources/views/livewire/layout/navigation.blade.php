<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->

    <div class="w-full m-auto flex flex-row items-center ">
        <div class="w-1/12 flex justify-end">

                <div class="flex flex-row items-center min-w-12 border rounded-full p-2  {{ \App\Helpers\SortimentHelper::getBGColorClass($sortiment) }} text-white">
                    <x-fluentui-box-16-o class="w-7 pr-1" />
                    <!-- x-fluentui-person-32-o class="w-7 " / -->
                    <div>{{ $sortiment }}</div>
                </div>

        </div>
        <div class="w-11/12 flex justify-between h-20">
            <div class="flex justify-between w-full">

                <!-- Settings Dropdown -->
                @if (Auth::user())
                    <div class="hidden sm:flex sm:items-center min-w-48 max-w-56">
                        <x-dropdown align="left" >
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center pr-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">

                                    <div x-data="{{ json_encode([ 'navText' => $navText ]) }}"
                                        x-html="navText"
                                        x-on:profile-updated.window="navText = $event.detail.navText"
                                        class="pl-2 text-left">
                                    </div>

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
                                @foreach ($kunden as $kunde)
                                    <x-dropdown-link wire:click="changeDebitor({{ $kunde->nr }})" href="#">
                                        <div class="flex flex-row item-center">
                                            @if ($kunde->nr === $this->debitornr )
                                                <div class="text-ewe-gruen pr-2"> <x-fluentui-checkbox-checked-24 class='h-5' /> </div>
                                            @else
                                                <div class="text-gray-300 pr-2"> <x-fluentui-checkbox-checked-24-o class='h-5' /> </div>
                                            @endif
                                            <div class="pr-1">{{ $kunde->nr}} - {{ $kunde->name }} - </div>
                                            @php
                                                $fontColor = 'text-'.strtolower($kunde->sortiment);
                                            @endphp

                                            <div class="{{ \App\Helpers\SortimentHelper::getColorClass($kunde->sortiment) }} pr-1">
                                                <x-fluentui-checkbox-indeterminate-16-o class="h-5" />
                                            </div>

                                            <div>{{ $kunde->sortiment }}</div>

                                        </div>
                                    </x-dropdown-link>
                                @endforeach
                                <x-dropdown-hr />


                                @if (auth()->user()->isAdmin())


                                    <x-dropdown-link :href="route('apitest')" wire:navigate class="bg-red-50">
                                        {{ __('API-Test') }}
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('apilog')" wire:navigate class="bg-red-50">
                                        {{ __('API-Log') }}
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('logs')" wire:navigate class="bg-red-50">
                                        {{ __('Log-Datei') }}
                                    </x-dropdown-link>

                                    <x-dropdown-hr />

                                @endif


                                <x-dropdown-link :href="route('profile')" wire:navigate>
                                    {{ __('auth.Profile') }}
                                </x-dropdown-link>

                                <!-- Authentication -->
                                <button wire:click="logout" class="w-full text-start">
                                    <x-dropdown-link>
                                        {{ __('auth.Log Out') }}
                                    </x-dropdown-link>
                                </button>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif
                <div class="flex items-center m-auto w-full justify-between">
                    <!-- img src="{{ asset('storage/c-1.png') }}" -->

                    <!-- Logo -->
                    <div class="flex shrink sm:min-w-40 min-w-28"> <!-- SM Logo min -->
                        <x-ewe-logo class="w-full" />
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex ">
                        <x-nav-link :href="route('startseite')" :active="request()->routeIs('startseite')" wire:navigate>
                            {{ __('Startseite') }} <!-- /*  ###########################  */ -->
                        </x-nav-link>

                        @if (Auth::user())
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <x-nav-link :href="route('bestellungen')" :active="request()->routeIs('bestellungen')" wire:navigate>
                                    <x-fluentui-building-shop-16-o class="w-5 h-5 mr-2" />
                                    {{ __('Bestellungen') }} <!-- /*  ###########################  */ -->

                                </x-nav-link>
                            </div>
                        @endif

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
<!--
                            <x-dropdown align="right" width="48" class="">
                                <x-slot name="trigger">
                                    <button
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                        <div>Kunden</div>

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
                                    <x-dropdown-link :href="route('anschriften')" wire:navigate>
                                        {{ __('Kunden - Anschriften') }}
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        -->

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


                        @if (Auth::user())
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <x-nav-link :href="route('shop')" :active="request()->routeIs('shop')" wire:navigate>
                                    <x-fluentui-building-shop-16-o class="w-5 h-5 mr-2" />
                                    {{ __('Shop') }} <!-- /*  ###########################  */ -->

                                </x-nav-link>
                            </div>
                        @endif
                    </div>
                </div>
            </div>





            @if (Auth::user())
                <div class="hidden sm:flex w-[20vh]">
                    <div class="flex flex-col sm:items-center sm:ms-6">
                        <x-nav-link :href="route('shop',[ 'tab' => 'tab5'] )" :active="request()->routeIs('bestellungen')" wire:navigate>
                            <x-fluentui-shopping-bag-20-o class="w-8 h-8" />
                            <div class="text-5xl text-sky-600">{{ $bestellung->anzpositionen }}</div>
                        </x-nav-link>
                        <div class="text-xs text-gray-50 bg-gray-500 px-2">{{ formatGPreis($bestellung->gesamtbetrag) }} €</div>

                    </div>

                </div>
            @endif


            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('startseite')" :active="request()->routeIs('startseite')" wire:navigate>
                {{ __('Startseite') }}
            </x-responsive-nav-link>


            <x-responsive-nav-link :href="route('artikel')" :active="request()->routeIs('artikel')" wire:navigate>
                {{ __('Artikel') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('sortimente')" :active="request()->routeIs('sortimente')" wire:navigate>

                {{ __('Sortimente') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('warengruppen')" :active="request()->routeIs('warengruppen')" wire:navigate>
                {{ __('Warengruppen') }} <!-- /*  ###########################  */ -->
            </x-responsive-nav-link>


            <x-responsive-nav-link :href="route('shop')" :active="request()->routeIs('shop')" wire:navigate class="border-t border-gray-200 dark:border-gray-600">
                <x-fluentui-building-shop-16-o class="w-5 h-5 mr-2 float-left" />
                {{ __('Shop') }} <!-- /*  ###########################  */ -->

            </x-responsive-nav-link>

        </div>
        @if (auth()->user())
            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200" x-data="{{ json_encode(['name' => auth()->user()->name]) }}"
                        x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                    <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile')" wire:navigate>
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <button wire:click="logout" class="w-full text-start">
                        <x-responsive-nav-link>
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </button>
                </div>
            </div>
        @endif
    </div>
    <script>
        window.addEventListener('page-reload', () => {
            window.location.reload();
        });
    </script>
</nav>
