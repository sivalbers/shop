@props([
    'text' => 'Möchten Sie diese Aktion wirklich ausführen?',
    'onJa' => 'jaBestätigt', // Livewire Event bei "Ja"
    'onNein' => '',          // optional, z. B. zum Loggen oder Schließen
])

<div x-data="{ offen: @entangle('zeigeModal').defer }"
     x-show="offen"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-sm">

    <div @click.outside="offen = false"
         class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">

        <h2 class="text-lg font-bold mb-4 text-gray-800">
            {{ $text }}
        </h2>

        <div class="flex justify-end space-x-4 mt-6">
            <button @click="offen = false; $wire.call('{{ $onNein }}')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                Nein
            </button>
            <button @click="offen = false; $wire.call('{{ $onJa }}')" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                Ja
            </button>
        </div>
    </div>
</div>
