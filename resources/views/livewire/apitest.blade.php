<div>

    <div class="mb-4 text-sm">
        <div class="flex flex-col w-11/12 m-auto border flatwhite">
            <div class="flex flex-row m-2 font-bold text-xl text-sky-600">
                API-Verwaltung
            </div>
            <div class="flex flex-row ml-2">
                <div class="w-[3%] border-b">
                    ID
                </div>
                <div  class="w-[20%] border-b">
                    Applikationsname
                </div>
                <div class="w-[20%] border-b">
                    API-Key
                </div>
                <div class="w-1/12 border-b">
                    Session-ID
                </div>
                <div class="w-1/12 border-b">
                    Session-Ende
                </div>
                <div class="w-1/12 border-b">
                    Letztes Login
                </div>
                <div class="w-1/12 border-b">
                    Status
                </div>
                <div class="w-2/12 border-b">
                    Erlaubte-Endpunkte
                </div>
            </div>
        @foreach ($ApplicationAuth as $auth)
            <div class="flex flex-row ml-2">
                <div class="w-[3%]">
                    <a href="#" wire:click="edit({{ $auth->id }})">
                        {{ $auth->id }}
                    </a>
                </div>
                <div  class="w-[20%]">
                    <div class="flex flex-row">
                        <div>
                            <a href="#" wire:click="setApplikationsnameAsUrl('{{ $auth->applicationname }}')">
                                <x-fluentui-clipboard-checkmark-24-o class="h-5 cursor-pointer hover:text-blue-500" />
                            </a>
                        </div>
                        <div class="ml-2">
                            {{ $auth->applicationname }}
                        </div>
                    </div>
                </div>
                <div class="w-[20%]" x-data>
                    <div class="flex flex-row">
                        <div>
                            <a href="#" wire:click="setTestApiKey('{{ $auth->apikey }}')">
                                <x-fluentui-clipboard-checkmark-24-o class="h-5 cursor-pointer hover:text-blue-500" />
                            </a>
                        </div>
                        <div class="ml-2">
                            {{ $auth->apikey }}
                        </div>
                    </div>
                </div>



                <div class="w-1/12">
                    {{ $auth->sessionid }}
                </div>
                <div class="w-1/12">
                    {{ $auth->sessionexpiry }}
                </div>
                <div class="w-1/12">
                    {{ $auth->lastlogin }}
                </div>
                <div class="w-1/12">
                    {{ $auth->status }}
                </div>
                <div class="w-2/12">
                    {{ implode(', ', $auth->allowedendpoints) }}
                </div>
            </div>
        @endforeach
        </div>
    </div>

    <div class="w-11/12 m-auto flatwhite mb-4">
        <div>
            <div class="flex flex-row m-2 font-bold text-xl text-sky-600">
                Eintrag ändern / <a href="#" wire:click.prevent="neu">neu</a>
            </div>
            <form wire:submit.prevent="save" class="w-full m-4">
                <div class="mb-0 flex flex-row items-center ">
                    <div class="w-2/12 mr-2" >
                        <label for="appName" class="block font-bold">Applikationsname:</label>
                    </div>
                    <div class="w-3/12 mr-2">
                        <label for="apiKey" class="block font-bold">API-Key:</label>
                    </div>
                    <div class="w-3/12 mr-2">
                        <label for="sessionId" class="block font-bold">Session-ID:</label>
                    </div>
                    <div class="w-1/12 mr-2">
                        <label for="sessionexpiry" class="block font-bold">Session-Ende:</label>
                    </div>
                    <div class="w-1/12 mr-2">
                        <label for="status" class="block font-bold">Status:</label>
                    </div>

                </div>
                <div class="mb-0 flex flex-row items-center ">
                    <div class="w-2/12 mr-2">
                        <input type="hidden" id="id" wire:model="id">
                        <input type="text" id="appName" wire:model="appName" class="border rounded p-1 w-full">
                    </div>

                    <div class="w-3/12 mr-2">
                        <input type="text" id="apiKey" wire:model="apiKey" class="border rounded p-1 w-full">
                    </div>
                    <div class="w-3/12 mr-2">
                        <input type="text" id="sessionId" wire:model="sessionId" class="border rounded p-1 w-full">
                    </div>
                    <div class="w-1/12 mr-2">
                        <input type="datetime-local" id="sessionexpiry" wire:model="sessionexpiry" class="border rounded p-1 w-full">
                    </div>
                    <div class="w-1/12 mr-2">
                        <input type="text" id="status" wire:model="status" class="border rounded p-1 w-full" list="statuslist">
                            <datalist id="statuslist">
                                <option value="active">
                                <option value="inactive">
                                <option value="revoked">
                            </datalist>
                    </div>
                    <div class="w-1/12 items-center">
                        <button type="submit" class="bg-sky-600 text-white px-4 py-2 rounded ">Speichern</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="w-11/12 m-auto flatwhite mb-4">
        <div>
            <div class="flex flex-row m-2 font-bold text-xl text-sky-600">
                Token erstellen aus Aufruf und API-KEY
            </div>
            <form  class="w-full m-4">
                <div class="mb-0 flex flex-row items-center ">
                    <div class="w-2/6 mr-4" >
                        <label for="aufruf" class="block font-bold">Aufruf:</label>
                    </div>
                    <div class="w-3/6 ">
                        <label for="testApiKey" class="block font-bold">API-Key:</label>
                    </div>

                </div>
                <div class="mb-1 flex flex-row items-center ">
                    <div class="w-2/6 mr-4">
                        <input type="text" id="aufruf" wire:model="aufruf" class="border rounded p-1 w-full">
                    </div>

                    <div class="w-3/6 mr-4">
                        <input type="text" id="testApiKey" wire:model="testApiKey" class="border rounded p-1 w-full">
                    </div>

                    <div class="w-1/6 items-center">
                        <button type="button" wire:click="buildToken" class="bg-sky-600 text-white px-4 py-2 rounded ">Build-Token</button>
                    </div>
                </div>
                <div class="mb-2 flex flex-row items-center ">
                    <div class="mr-2 w-48">API-Key-hash:</div>
                    <div class="w-[50%]">
                    <input type="text" id="testApiKeyHash" wire:model="testApiKeyHash" class="border rounded p-1 w-full">
                    </div>
                </div>
                <div class="mb-0 flex flex-row items-center ">
                    <div class="mr-2  w-48">Token:</div>
                    <div class="w-[50%]">
                    <input type="text" id="token" wire:model="token" class="border rounded p-1 w-full">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="w-11/12 m-auto flatwhite mb-4">
        <div>
            <div class="flex flex-row m-2 font-bold text-xl text-sky-600">
                URL Testen
            </div>
            <form  class="w-full m-4">
                <div class="mb-0 flex flex-row items-center ">
                    <div class="w-3/12 mr-4" >
                        <label for="testUrl" class="block font-bold">URL:</label>
                    </div>

                    <div class="w-2/12 mr-4" >
                        <label for="aufruf" class="block font-bold">Aufruf:</label>
                    </div>
                    <div class="w-3/12 ">
                        <label for="testApiKey" class="block font-bold">API-Key:</label>
                    </div>

                </div>
                <div class="mb-1 flex flex-row items-center ">
                    <div class="w-3/12 mr-4">
                        <input type="text" id="testUrl" wire:model="testUrl" class="border rounded p-1 w-full">
                    </div>

                    <div class="w-2/12 mr-4">
                        <input type="text" id="aufruf" wire:model="aufruf" class="border rounded p-1 w-full">
                    </div>

                    <div class="w-3/12 mr-4">
                        <input type="text" id="testApiKey" wire:model="testApiKey" class="border rounded p-1 w-full">
                    </div>

                    <div class="w-1/12 items-center">
                        <button type="button" wire:click="funcTestUrl" class="bg-sky-600 text-white px-4 py-2 rounded ">Ausführen</button>
                    </div>
                </div>
                <div class="mb-1 flex flex-row items-center ">
                    <div class="mr-2 w-24">Token:</div>
                    <div class="w-[50%] mr-4 ">
                        <input type="text" id="token" wire:model="token" class="border rounded p-1 w-full">
                    </div>
                    <div class="text-xl text-red-500" >{{ $statusMessage }}</div>
                </div>

                <div class="mb-1 flex flex-row items-center ">
                    <div class="mr-2 w-24">Ergebnis:</div>
                    <div class="w-[50%]">

                        <textarea id="testResult" wire:model="testResult" class="border rounded p-1 w-full h-80"></textarea>

                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
