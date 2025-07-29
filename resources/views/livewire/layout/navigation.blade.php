<nav x-data="{ open: false }" class="bg-white ">
    <!-- Primary Navigation Menu -->

    @livewire('UserNachrichten')




    <!-- nichts und SM -->
    <div class="flex flex-row w-full md:hidden max-w-full">

        <div class="flex flex-col w-full" >

            <div class="flex flex-row w-full items-center  justify-between bg-white">  <!-- zeile oben logo - suche - zeugnisarchiv -->

                <div class="flex-none"> <!-- SM Logo min -->
                    <a href="{{ route('startseite') }}">
                        <x-ewe-logo class="h-8 " />
                    </a>
                </div>

                <div class="flex-grow max-w-[200px]">
                    <livewire:artikel-suche :key="'suchtest'" />
                </div>

                <div class="flex-none pl-2 pt-1">
                    <x-nav-link :href="route('news')" :active="request()->routeIs('news')" wire:navigate class=" ">
                        <x-fluentui-news-20-o class="w-8 h-8  text-gray-600" />
                        <div class="text-2xl text-sky-600">{{ $anzNachrichten > 0 ? $anzNachrichten : '' }}</div>
                    </x-nav-link>
                </div>
            </div>


            <div class="flex flex-row w-full justify-between bg-ewe-gruen text-gray-500"> <!-- Link, Shop und Bestellungen -->
                <div class="text-xs">
                    <x-nav-link
                        class="text-xs"
                        href="https://zeugnisse.netzmaterialonline.de"
                        target="_blank"
                        rel="noopener"
                        :compact="true">
                        <x-fluentui-link-16-o class="w-6 h-6 mr-1" />
                        Zeugnis-<br>archiv
                    </x-nav-link>
                </div>

                <div class=" h-12 space-x-4 flex justify-center text-gray-600">
                    <x-nav-link

                        :href="route('shop')"
                        :active="request()->routeIs('shop') | request()->routeIs('startseite')"
                        wire:navigate
                        rel="noopener"
                        :compact="true">
                        <x-fluentui-building-shop-16-o class="w-5 h-5 mr-1" />
                        {{ __('Shop') }}
                    </x-nav-link>

                    <x-nav-link :href="route('bestellungen')" :active="request()->routeIs('bestellungen')" wire:navigate>
                        <x-fluentui-text-bullet-list-square-clock-20-o class="w-6 h-6 mr-1" />
                        {{ __('Bestellungen') }} <!-- /*  ###########################  */ -->
                    </x-nav-link>
                </div>

                <div class="flex flex-row justify-end">
                    <div class="inline-flex flex-col sm:items-center ">
                        <x-nav-link :href="route('shop', ['activeTab' => 'warenkorb'])" :active="request('activeTab') === 'warenkorb'" wire:navigate class=" h-12 px-4">
                            <x-fluentui-shopping-bag-20-o class="w-8 h-8 text-gray-600" />
                            <div class="text-5xl text-sky-600">{{ !empty($bestellung) ? $bestellung->anzpositionen : '' }}</div>
                        </x-nav-link>
                        <!-- Preis-Anzeige -->
                        <div class="">
                            <div class="text-xs
                                text-sky-600
                                w-full text-right  px-4">

                                {{ !empty($bestellung) ? formatGPreis($bestellung->gesamtbetrag) : '' }} €
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <div class="flex flex-row w-full bg-ewe-gruen border-t border-gray-500" >
                <div class="h-12 flex grow-0  bg-inherit/50"> <!-- Zeile mit Name -->

                    @if ( !empty(Auth::user()) && auth()->user()->isAdmin())
                        <x-dropdown align="right">
                            <x-slot name="trigger">
                                <button
                                    class="flex flex-row p-2 text-sm text-gray-500
                                        focus:outline-none transition ease-in-out duration-150 ">
                                    <div class="flex flex-row w-full text-left items-center">
                                        <div>
                                            <x-fluentui-person-16-o class="w-8 h-8" />
                                        </div>
                                        <div class="text-sky-600">
                                            {{ Auth::user()->name }}
                                        </div>

                                        @if (auth()->user()->isAdmin())
                                        <div>
                                            <x-dropdown-svg />
                                        </div>
                                        @endif

                                    </div>

                                </button>
                            </x-slot>

                            <x-slot name="content">


                                    <x-dropdown-hr />
                                    <x-dropdown-link :href="route('apitest')" wire:navigate class="bg-red-50">
                                        {{ __('API-Test') }}
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('apilog')" wire:navigate class="bg-red-50">
                                        {{ __('API-Log') }}
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('logs')" wire:navigate class="bg-red-50">
                                        {{ __('Log-Datei') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('belegarchiv')" wire:navigate class="bg-red-50">
                                        <div class="flex flex-row">
                                            <x-fluentui-database-search-20-o class="w-6 h-6 mr-1" />
                                            {{ __('Belegarchiv') }}
                                        </div>
                                    </x-dropdown-link>
                                    <x-dropdown-hr />


                            </x-slot>

                        </x-dropdown>
                    @else
                    <div class="flex flex-row p-2 text-sm  w-full text-left items-center">
                        <div>
                            <x-fluentui-person-16-o class="w-8 h-8 text-gray-600" />
                        </div>
                        <div class="text-sky-600">
                            {{ !empty(Auth::user()) ? Auth::user()->name : '' }}
                        </div>

                    </div>

                    @endif
                </div>

                <div class="h-12 grow  flex justify-end items-center pr-2"> <!-- Name und Sortiment - Dropdown -->
                    <x-dropdown align="right">
                        <x-slot name="trigger"> <!-- Name und Sortiment - Überschrift -->
                            <button class="inline-flex items-center pl-2 pt-2 text-sm focus:outline-none transition ease-in-out duration-150 w-full
                                bg-ewe-gruen">
                                <div class=" text-start text-gray-600">

                                    <div>{{ $debitornr }} - {{ $firma }}</div>
                                    <div class="flex flex-row items-center text-sm ">
                                        <div class="">- {{ $sortimentName }}</div>
                                    </div>
                                </div>

                                <div class="">
                                    <x-dropdown-svg />
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content"> <!-- Auswahl der Mandanten -->
                            @if (!empty($kunden) && count($kunden) > 1)
                            <div class="text-xl  rounded-t-md pl-2 -mt-1 pt-1 h-12 flex items-center
                                text-sky-600
                                bg-ewe-gruen">
                                Mandantenauswahl:
                            </div>
                            <x-dropdown-hr />

                            @foreach ($kunden as $kunde)

                                <x-dropdown-link wire:click="changeDebitor({{ $kunde->nr }})" href="#"

                                    >
                                    <div class="inline-flex flex-col items-start px-2 py-1 whitespace-nowrap">
                                        <div class="flex flex-row items-center space-x-2">
                                            @if ($kunde->nr === $this->debitornr)
                                                <div class="text-ewe-gruen">
                                                    <x-fluentui-checkbox-checked-24 class='h-5' />
                                                </div>
                                            @else
                                                <div class="text-gray-300">
                                                    <x-fluentui-checkbox-checked-24-o class='h-5' />
                                                </div>
                                            @endif

                                            <div class="font-medium">
                                                {{ $kunde->nr }} {{ $kunde->name }}
                                            </div>
                                        </div>
                                        <div class="flex flex-row items-center mt-1 text-sm ml-6 ">
                                            <div class="{{ \App\Helpers\SortimentHelper::getColorClass($kunde->sortiment) }} pr-1">
                                                <x-fluentui-checkbox-indeterminate-16-o class="h-5" />
                                            </div>
                                            <div class="">{{ $kunde->sortimentName() }}</div>
                                        </div>
                                    </div>

                                </x-dropdown-link>
                                <x-dropdown-hr />

                            @endforeach
                            @endif
                            <div class="text-xl mt-0 pl-2 pt-1 text-sky-600 bg-ewe-gruen px-8 border-b border-gray-600 h-12 flex items-center">
                                Benutzer:
                            </div>
                            <x-dropdown-link :href="route('profile')" wire:navigate>
                                {{ __('auth.Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <button wire:click="logout" class="w-full text-start">
                                <x-dropdown-link >
                                    {{ __('auth.Log Out') }}
                                </x-dropdown-link>
                            </button>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            @if ( !empty(Auth::user()) && auth()->user()->isAdmin() && true === false )
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            @endif
        </div>
    </div>




    <!-- ab md -->
    <div class="hidden md:flex flex-row w-full">

        <div class="flex w-full h-24  ">


            <div class="flex-grow justify-between" ><!-- zeile oben logo - suche - zeugnisarchiv -->

                <div class="flex flex-col w-full ">
                    <div class=" md:pl-8 md:h-12 w-full">
                        <div class="flex flex-col md:flex-row ">
                            <div class="flex flex-none sm:min-w-40 min-w-28 border-blue-500"> <!-- SM Logo min -->
                                <a href="{{ route('startseite') }}">
                                    <x-ewe-logo class="h-14 " />
                                </a>
                            </div>

                            <div class="flex flex-row w-full  border-pink-500">
                                <livewire:artikel-suche :key="'suchtest'" />
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-row bg-ewe-gruen text-gray-500">
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex text-xs">
                            <x-nav-link
                                class="text-xs"
                                href="https://zeugnisse.netzmaterialonline.de"
                                target="_blank"
                                rel="noopener"
                                :compact="true">
                                <x-fluentui-link-16-o class="w-6 h-6 mr-1" />
                                {{ __('Zeugnisarchiv') }}
                            </x-nav-link>
                        </div>

                        <div class=" h-12 space-x-8 flex justify-end w-full text-gray-600">


                                <x-nav-link

                                    :href="route('shop')"
                                    :active="request()->routeIs('shop') | request()->routeIs('startseite')"
                                    wire:navigate
                                    rel="noopener"
                                    :compact="true">
                                    <x-fluentui-building-shop-16-o class="w-5 h-5 mr-1" />
                                    {{ __('Shop') }}
                                </x-nav-link>




                                <x-nav-link :href="route('bestellungen')" :active="request()->routeIs('bestellungen')" wire:navigate>

                                    <x-fluentui-text-bullet-list-square-clock-20-o class="w-6 h-6 mr-1" />

                                    {{ __('Bestellungen') }} <!-- /*  ###########################  */ -->

                                </x-nav-link>


                        </div>
                    </div>


                </div>

            </div>

            <div class="flex h-28  pb-4"> <!-- Nachrichten -->
                <div class="flex flex-row justify-between w-full">
                    <div class="inline-flex flex-col sm:items-center ">
                        <x-nav-link :href="route('news')" :active="request()->routeIs('news')" wire:navigate class=" h-12 px-4 pt-2">
                            <x-fluentui-news-20-o class="w-8 h-8  text-gray-600" />
                            <div class="text-2xl text-sky-600">{{ $anzNachrichten > 0 ? $anzNachrichten : '' }}</div>
                        </x-nav-link>
                        <!-- Preis-Anzeige -->
                        <div class="h-12 w-full bg-ewe-gruen">
                            <div class="text-xs
                                text-sky-600
                                 w-full text-right  px-4">


                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="flex h-28  pb-4"> <!-- Anzahl und preis -->
                <div class="flex flex-row justify-between w-full">
                    <div class="inline-flex flex-col sm:items-center ">
                        <x-nav-link :href="route('shop', ['activeTab' => 'warenkorb'])" :active="request('activeTab') === 'warenkorb'" wire:navigate class=" h-12 px-4">
                            <x-fluentui-shopping-bag-20-o class="w-8 h-8 text-gray-600" />
                            <div class="text-5xl text-sky-600">{{ !empty($bestellung) ? $bestellung->anzpositionen : '' }}</div>
                        </x-nav-link>
                        <!-- Preis-Anzeige -->
                        <div class="h-12 w-full bg-ewe-gruen">
                            <div class="text-xs
                                text-sky-600
                                 w-full text-right  px-4">

                                {{ !empty($bestellung) ? formatGPreis($bestellung->gesamtbetrag) : '' }} €
                            </div>
                        </div>

                    </div>
                </div>
            </div>


            <div class="flex flex-col ">
                <div class="h-12 flex  w-full bg-inherit/50"> <!-- Zeile mit Name -->

                    @if ( !empty(Auth::user()) && auth()->user()->isAdmin())
                        <x-dropdown align="right">
                            <x-slot name="trigger">
                                <button
                                    class="flex flex-row p-2 text-sm text-gray-500
                                        focus:outline-none transition ease-in-out duration-150 ">
                                    <div class="flex flex-row w-full text-left items-center">
                                        <div>
                                            <x-fluentui-person-16-o class="w-8 h-8" />
                                        </div>
                                        <div class="text-sky-600">
                                            {{ Auth::user()->name }}
                                        </div>

                                        @if (auth()->user()->isAdmin())
                                        <div>
                                            <x-dropdown-svg />
                                        </div>
                                        @endif

                                    </div>

                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-hr />
                                <x-dropdown-link :href="route('apitest')" wire:navigate class="bg-red-50">
                                    {{ __('API-Test') }}
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('apilog')" wire:navigate class="bg-red-50">
                                    {{ __('API-Log') }}
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('logs')" wire:navigate class="bg-red-50">
                                    {{ __('Log-Datei') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('belegarchiv')" wire:navigate class="bg-red-50">
                                    <div class="flex flex-row">
                                        <x-fluentui-database-search-20-o class="w-6 h-6 mr-1" />
                                        {{ __('Belegarchiv') }}
                                    </div>
                                </x-dropdown-link>
                                <x-dropdown-hr />
                            </x-slot>

                        </x-dropdown>
                    @else
                    <div class="flex flex-row p-2 text-sm  w-full text-left items-center">
                        <div>
                            <x-fluentui-person-16-o class="w-8 h-8 text-gray-600" />
                        </div>
                        <div class="text-sky-600">
                            {{ !empty(Auth::user()) ? Auth::user()->name : '' }}
                        </div>

                    </div>

                    @endif
                </div>

                <div class="h-12 w-full">
                    <x-dropdown align="right">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center pl-2 pt-2 text-sm focus:outline-none transition ease-in-out duration-150 w-full
                                bg-ewe-gruen">
                                <div class=" text-start text-gray-600">

                                    <div>{{ $debitornr }} - {{ $firma }}</div>
                                    <div class="flex flex-row items-center text-sm ">
                                        <div class="">- {{ $sortimentName }}</div>
                                    </div>
                                </div>

                                <div class="">
                                    <x-dropdown-svg />
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                                @if (!empty($kunden) && count($kunden) > 1)
                                <div class="text-xl  rounded-t-md pl-2 -mt-1 pt-1 h-12 flex items-center
                                    text-sky-600
                                    bg-ewe-gruen">
                                    Mandantenauswahl:
                                </div>
                                <x-dropdown-hr />

                                @foreach ($kunden as $kunde)

                                    <x-dropdown-link wire:click="changeDebitor({{ $kunde->nr }})" href="#"

                                        >
                                        <div class="inline-flex flex-col items-start px-2 py-1 whitespace-nowrap">
                                            <div class="flex flex-row items-center space-x-2">
                                                @if ($kunde->nr === $this->debitornr)
                                                    <div class="text-ewe-gruen">
                                                        <x-fluentui-checkbox-checked-24 class='h-5' />
                                                    </div>
                                                @else
                                                    <div class="text-gray-300">
                                                        <x-fluentui-checkbox-checked-24-o class='h-5' />
                                                    </div>
                                                @endif

                                                <div class="font-medium">
                                                    {{ $kunde->nr }} {{ $kunde->name }}
                                                </div>
                                            </div>
                                            <div class="flex flex-row items-center mt-1 text-sm ml-6 ">
                                                <div class="{{ \App\Helpers\SortimentHelper::getColorClass($kunde->sortiment) }} pr-1">
                                                    <x-fluentui-checkbox-indeterminate-16-o class="h-5" />
                                                </div>
                                                <div class="">{{ $kunde->sortimentName() }}</div>
                                            </div>
                                        </div>

                                    </x-dropdown-link>
                                    <x-dropdown-hr />

                                @endforeach
                                @endif
                                <div class="text-xl mt-0 pl-2 pt-1 text-sky-600 bg-ewe-gruen px-8 border-b border-gray-600 h-12 flex items-center">
                                    Benutzer:
                                </div>
                                <x-dropdown-link :href="route('profile')" wire:navigate>
                                    {{ __('auth.Profile') }}
                                </x-dropdown-link>

                                <!-- Authentication -->
                                <button wire:click="logout" class="w-full text-start">
                                    <x-dropdown-link >
                                        {{ __('auth.Log Out') }}
                                    </x-dropdown-link>
                                </button>
                        </x-slot>
                    </x-dropdown>
                </div>


            </div>
            <div class="flex h-28"> <!-- Anzahl und preis -->
                <div class="flex flex-col">
                        <div class="h-12 w-8"></div>
                        <div class="h-12 w-8 bg-ewe-gruen"> </div>
                </div>
            </div>

        </div>

        @if ( !empty(Auth::user()) && auth()->user()->isAdmin())
        <!-- Hamburger -->
        <div class="-me-2 flex items-center sm:hidden">
            <button @click="open = ! open"
                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        @endif
    </div>




    @include('livewire.layout.navigation-response')
    <script>
        window.addEventListener('page-reload', () => {
            window.location.reload();
        });
    </script>
</nav>
