<nav x-data="{ open: false }" class="bg-white ">
    <!-- Primary Navigation Menu -->



    <span class="hidden hover:bg-ewe-gruen hover:bg-green-500 hover:bg-pink-100 hover:bg-pink-500 hover:bg-blue-500 hover:bg-orange-500 hover:bg-gray-100 hover:bg-yellow-100"></span>
    <div class="">
    <div class="flex flex-row w-full ">
        <div class="flex w-full h-24  ">
            <div class="flex-grow justify-between"><!-- zeile oben logo - suche - zeugnisarchiv -->
                <div class="flex items-center w-full">
                    <div class="flex flex-col w-full">
                        <div class=" h-12">
                            <div class="flex flex-row items-center justify-between">

                                <div class="flex shrink sm:min-w-40 min-w-28"> <!-- SM Logo min -->
                                    <a href="{{ route('startseite') }}">
                                        <x-ewe-logo class="h-14 " />
                                    </a>
                                </div>

                                <div class="flex flex-row w-56">
                                    <livewire:artikel-suche :key="'suchtest'" />
                                </div>

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

                            </div>
                        </div>
                        <div class="h-12 py-4 space-x-8 flex text-white justify-end ">

                            <div class="space-x-8 flex">
                                <x-nav-link
                                    :href="route('shop')"
                                    :active="request()->routeIs('shop')"
                                    wire:navigate>
                                    <x-fluentui-building-shop-16-o class="w-5 h-5 mr-1" />
                                    {{ __('Shop') }}
                                </x-nav-link>
                            </div>


                            <div class="space-x-8 flex">
                                <x-nav-link :href="route('news')" :active="request()->routeIs('news')" wire:navigate>

                                    <x-fluentui-news-20-o class="w-6 h-6 mr-1" />
                                    <div class="font-bold flex flex-row items-center">
                                        {{ __('Nachrichten') }} (3)<!-- /*  ###########################  */ -->
                                    </div>

                                </x-nav-link>
                            </div>


                            <div class="space-x-8 flex">
                                <x-nav-link :href="route('bestellungen')" :active="request()->routeIs('bestellungen')" wire:navigate>

                                    <x-fluentui-text-bullet-list-square-clock-20-o class="w-6 h-6 mr-1" />

                                    {{ __('Bestellungen') }} <!-- /*  ###########################  */ -->

                                </x-nav-link>
                            </div>

                        </div>

                        <!-- include('livewire.layout.navigation-admin') -->

                    </div>
                </div>
            </div>


            <div class="flex h-28 mx-8 px-4 pb-4"> <!-- Anzahl und preis -->
                <div class="flex flex-row justify-between w-full">
                    <div class="inline-flex flex-row sm:items-center">
                        <x-nav-link :href="route('shop', ['activeTab' => 'warenkorb'])" wire:navigate class=" h-12">
                            <x-fluentui-shopping-bag-20-o class="w-8 h-8" />
                            <div class="text-5xl text-sky-600">{{ $bestellung->anzpositionen }}</div>
                        </x-nav-link>
                        <!-- Preis-Anzeige -->
                        <div class="h-12 w-full">
                            <div class="text-xs
                                {{ \App\Helpers\SortimentHelper::getTextZuBGColorClass($sortiment) }}
                                {{ \App\Helpers\SortimentHelper::getBGColorClass($sortiment) }} w-full text-right pr-1">

                                {{ formatGPreis($bestellung->gesamtbetrag) }} €
                            </div>
                        </div>

                    </div>
                </div>
            </div>



            <div class="flex flex-col h-28 ">
                <div class="h-12 flex  w-full bg-opacity-50 "> <!-- Zeile mit Name -->
                    @if (auth()->user()->isAdmin())
                        <x-dropdown align="right">
                            <x-slot name="trigger">
                                <button
                                    class="flex flex-row p-2 text-sm text-gray-500
                                        focus:outline-none transition ease-in-out duration-150 ">
                                    <div class="w-full text-left ">
                                        Hallo {{ Auth::user()->name }}
                                    </div>
                                    @if (auth()->user()->isAdmin())
                                    <x-dropdown-svg />
                                    @endif
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
                    <div class="flex flex-row p-2 text-sm text-gray-500 w-full text-left ">
                        Hallo {{ Auth::user()->name }}
                    </div>

                    @endif
                </div>

                <div class="h-12 w-full">
                    <x-dropdown align="right">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center pl-2 pt-2 text-sm focus:outline-none transition ease-in-out duration-150 w-full">
                                <div class="text-gray-500 text-start">

                                    <div>{{ $debitornr }} - {{ $firma }}</div>
                                    <div class="flex flex-row items-center mb-1 text-sm ">
                                        <div class="{{ \App\Helpers\SortimentHelper::getColorClass($sortiment) }} pr-1">
                                            <x-fluentui-checkbox-indeterminate-16-o class="h-5" />
                                        </div>
                                        <div class="">{{ $sortimentName }}</div>
                                    </div>
                                </div>

                                <div class="">
                                    <x-dropdown-svg />
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">

                                    <div class="text-xl -mt-2 rounded-t-md pl-2 h-11 pt-2
                                    {{ \App\Helpers\SortimentHelper::getTextZuBGColorClass($sortiment) }}
                                    {{ \App\Helpers\SortimentHelper::getBGColorClass($sortiment) }}">
                                    Mandantenauswahl:
                                </div>
                                <x-dropdown-hr />
                                @php
                                    $hover = 'hover:' . \App\Helpers\SortimentHelper::getBGColorClass($sortiment);
                                @endphp
                                {{-- Tailwind-Klassen für den Purge-Prozess sichtbar machen --}}



                                @foreach ($kunden as $kunde)

                                    <x-dropdown-link wire:click="changeDebitor({{ $kunde->nr }})" href="#"
                                        :hover-class="$hover"
                                        >
                                        <div class="inline-flex flex-col items-start px-2 py-1 whitespace-nowrap">
                                            <div class="flex flex-row items-center space-x-2">
                                                @if ($kunde->nr === $this->debitornr)
                                                    <div class="{{ \App\Helpers\SortimentHelper::getColorClass($sortiment) }}">
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
                                            <div class="flex flex-row items-center mt-1 text-sm ml-6">
                                                <div class="{{ \App\Helpers\SortimentHelper::getColorClass($kunde->sortiment) }} pr-1">
                                                    <x-fluentui-checkbox-indeterminate-16-o class="h-5" />
                                                </div>
                                                <div class="">{{ $kunde->sortimentName() }}</div>
                                            </div>
                                        </div>

                                    </x-dropdown-link>
                                    <x-dropdown-hr />

                                @endforeach
                                <div class="text-xl mt-2 pl-2 h-8 pt-1
                                {{ \App\Helpers\SortimentHelper::getTextZuBGColorClass($sortiment) }}
                                {{ \App\Helpers\SortimentHelper::getBGColorClass($sortiment) }}">
                                Benutzer:
                            </div>
                                <x-dropdown-link :href="route('profile')" wire:navigate :hover-class="$hover">
                                    {{ __('auth.Profile') }}
                                </x-dropdown-link>

                                <!-- Authentication -->
                                <button wire:click="logout" class="w-full text-start">
                                    <x-dropdown-link :hover-class="$hover">
                                        {{ __('auth.Log Out') }}
                                    </x-dropdown-link>
                                </button>







                        </x-slot>
                    </x-dropdown>
                </div>


            </div>
        </div>

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
    </div>
    <div class="h-2 mt-0.5 w-full bg-gradient-to-t {{ \App\Helpers\SortimentHelper::getGradientBG($sortiment) }}" >
        &nbsp;
    </div>
    </div>


    @include('livewire.layout.navigation-response')
    <script>
        window.addEventListener('page-reload', () => {
            window.location.reload();
        });
    </script>
</nav>
