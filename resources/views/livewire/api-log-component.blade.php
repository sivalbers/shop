<div x-data="{ showApiLogEdit: @entangle('showApiLogEdit') }" x-cloak>
    <!-- Tabelle -->
    <div class="mb-4 text-sm">
        <div class="flex flex-col w-11/12 m-auto border flatwhite pb-4 overflow-x-auto">
            <div class="flex flex-row m-2 font-bold text-xl text-sky-600 justify-between">
                <div>API Logs</div>
                <div>
                    <a href="#" wire:click.prevent="resetForm" x-on:click="showApiLogEdit = true" class="text-sky-500">
                        <div class="flex flex-row item-center">
                            <x-fluentui-note-add-16-o class="h-6" /> Neu
                        </div>
                    </a>
                </div>
            </div>
            <div class="flex flex-row ml-2">
                <div class="min-w-40 w-[3%] border-b">Erstellt</div>
                <div class="min-w-20 w-[3%] border-b">Version</div>
                <div class="min-w-28 w-[9%] border-b">Method</div>
                <div class="min-w-56 w-[6%] border-b">Pfad</div>
                <div class="min-w-64 w-[27%] border-b">Key</div>
                <div class="min-w-64 w-[27%] border-b">Session</div>

                <div class="min-w-16 w-[3%] border-b">Aktionen</div>
            </div>
            @foreach ($logs as $log)
                <div class="flex flex-row ml-2">
                    <div class="min-w-40 w-[3%]">{{ $log->created_at->diffForHumans() }}</div>
                    <div class="min-w-20 w-[3%]">{{ $log->version }}</div>
                    <div class="min-w-28 w-[9%]">{{ $log->httpmethod }}</div>
                    <div class="min-w-56 w-[6%] truncate">{{ $log->pfad }}</div>
                    <div class="min-w-64 w-[27%] truncate">{{ $log->key }}</div>
                    <div class="min-w-64 w-[28%] truncate">{{ $log->session }}</div>

                    <div class="min-w-16 w-[3%] flex flex-row">
                        <a href="#" wire:click="edit({{ $log->id }})" x-on:click="showApiLogEdit = true"
                            title="Bearbeiten">
                            <x-fluentui-edit-16-o class="h-5" />
                        </a>
                        <a href="#" wire:click="delete({{ $log->id }})" title="Löschen">
                            <x-fluentui-delete-16-o class="h-5 text-red-600" />
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Formular -->
    <div class="w-full relative z-50">
        <div class="flex fixed top-0 bg-opacity-60 item-center w-full h-full bg-slate-100 backdrop-blur-[2px]"
            x-show="showApiLogEdit" x-on:click.self="showApiLogEdit = false">
            <div class="w-10/12 m-auto flatwhite">
                <div class="flex flex-row m-2 font-bold text-xl text-sky-600">
                    {{ $id ? 'Log bearbeiten' : 'Neues Log anlegen' }}
                </div>
                <form wire:submit.prevent="save" class="w-full m-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="version" class="block font-bold">Version:</label>
                            <input type="text" id="version" wire:model="version" class="border rounded p-1 w-full">
                        </div>
                        <div>
                            <label for="pfad" class="block font-bold">Pfad:</label>
                            <input type="text" id="pfad" wire:model="pfad" class="border rounded p-1 w-full">
                        </div>
                        <div>
                            <label for="key" class="block font-bold">Key:</label>
                            <input type="text" id="key" wire:model="key" class="border rounded p-1 w-full">
                        </div>
                        <div>
                            <label for="session" class="block font-bold">Session:</label>
                            <input type="text" id="session" wire:model="session" class="border rounded p-1 w-full">
                        </div>
                        <div>
                            <label for="token" class="block font-bold">Token:</label>
                            <input type="text" id="token" wire:model="token" class="border rounded p-1 w-full">
                        </div>
                        <div>
                            <label for="data" class="block font-bold">Data:</label>
                            <textarea id="data" wire:model="data" class="border rounded p-1 w-full"></textarea>
                        </div>
                        <div>
                            <label for="response" class="block font-bold">Response:</label>
                            <textarea id="response" wire:model="response" class="border rounded p-1 w-full"></textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="bg-sky-600 text-white px-4 py-2 rounded">Speichern</button>
                        <button type="button" x-on:click="showApiLogEdit = false"
                            class="bg-gray-600 text-white px-4 py-2 rounded">Abbrechen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
