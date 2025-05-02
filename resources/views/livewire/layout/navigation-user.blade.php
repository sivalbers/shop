                <!-- Settings Dropdown ******************************************************************************************
                     Settings Dropdown ******************************************************************************************
                     Settings Dropdown ******************************************************************************************
                -->
                @if (Auth::user())
                    <div class="hidden sm:flex sm:items-center min-w-48 w-2/12 bg-white  ">
                        <x-dropdown align="left"  width="{{  (count($kunden) > 1) ? 96 : 'w-56' }}">
                            <x-slot name="trigger" >
                                <button class="w-full bg-white inline-flex items-center ml-2 pl-2 py-2 text-sm leading-4 font-medium
                                    text-gray-500 dark:text-gray-400  dark:bg-gray-800
                                    hover:border hover:border-gray-600 rounded-md hover:shadow-md
                                    dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                    <div class="w-3/12 flex flex-row items-center min-w-12 border border-gray-400 rounded-full p-2  {{ \App\Helpers\SortimentHelper::getBGColorClass($sortiment) }} text-white">
                                        <x-fluentui-box-16-o class="min-w-5 pr-1" alt="{{ $sortimentName }}" />
                                        <div>{{ $sortimentName }}</div>
                                    </div>

                                    <div x-data="{{ json_encode([ 'navText' => $navText ]) }}"
                                        x-html="navText"
                                        x-on:profile-updated.window="navText = $event.detail.navText"
                                        class="w-8/12 pl-2 text-left ">
                                    </div>

                                    <div class=" w-1/12 ">
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
                                @if (count($kunden) > 1)
                                @foreach ($kunden as $kunde)
                                    @php
                                        $fontColor = 'text-'.strtolower($kunde->sortiment);
                                    @endphp

                                    <x-dropdown-link wire:click="changeDebitor({{ $kunde->nr }})" href="#">
                                        <div class="flex flex-row item-center">
                                            <div class="w-1/12">
                                                @if ($kunde->nr === $this->debitornr )
                                                    <div class="text-ewe-gruen pr-2"> <x-fluentui-checkbox-checked-24 class='h-5' /> </div>
                                                @else
                                                    <div class="text-gray-300 pr-2"> <x-fluentui-checkbox-checked-24-o class='h-5' /> </div>
                                                @endif
                                            </div>

                                            <div class="w-2/12 pr-1">
                                                {{ $kunde->nr}}
                                            </div>

                                            <div class="w-6/12 flex flex-row">
                                                {{ $kunde->name }}
                                            </div>

                                            <div class="w-3/12 flex flex-row">

                                                <div class="{{ \App\Helpers\SortimentHelper::getColorClass($kunde->sortiment) }} pr-1">
                                                    <x-fluentui-checkbox-indeterminate-16-o class="h-5" />
                                                </div>
                                                <div>{{ $kunde->sortimentName() }}</div>
                                            </div>

                                        </div>
                                    </x-dropdown-link>
                                @endforeach
                                <x-dropdown-hr />
                                @endif


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
                                    <x-dropdown-link :href="route('belegarchiv')" wire:navigate class="bg-red-50">
                                        <div class="flex flex-row">
                                             <x-fluentui-database-search-20-o class="w-6 h-6 mr-1" />
                                            {{ __('Belegarchiv') }}
                                        </div>
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
