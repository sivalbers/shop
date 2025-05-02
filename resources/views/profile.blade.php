<x-layouts.app>


    <div class="pb-12">
        <div class="max-w-full lg:max-w-full mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="w-full">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>
            @if (session()->get('rolle') === 1)
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="w-full">
                    <livewire:profile.update-user-information-form />
                </div>
            </div>
            @endif


            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>
