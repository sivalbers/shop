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
