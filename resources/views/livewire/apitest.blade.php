<div x-data="{ showApiEdit: @entangle('showApiEdit'), showApiSampleEdit: @entangle('showApiSampleEdit') }" x-cloak x-on:click.self="showApiEdit = false; showApiSampleEdit = false"
    x-on:keydown.escape.window="showApiEdit = false; showApiSampleEdit = false">

    <div class="mb-4 text-sm">
        <div class="flex flex-col w-11/12 m-auto border flatwhite pb-4">
            <div class="flex flex-row m-2 font-bold text-xl text-sky-600 justify-between">
                <div>
                    API-Verwaltung
                </div>
                <div>
                    <a href="#" wire:click.prevent="neu" class="text-sky-500">
                        <div class="flex flex-row item-center">
                            <x-fluentui-note-add-16-o class="h-6" /> Neu
                        </div>
                    </a>
                </div>

            </div>
            <div class="flex flex-row ml-2">
                <div class="w-[3%] border-b">
                    ID
                </div>
                <div class="w-[25%] border-b">
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
                        <a href="#" wire:click="edit({{ $auth->id }})" title="API - Bearbeiten">
                            {{ $auth->id }}
                        </a>
                    </div>
                    <div class="w-[25%]">
                        <div class="flex flex-row">
                            <div>
                                <a href="#" wire:click="setApplikationsnameAsUrl('{{ $auth->applicationname }}')">
                                    <x-fluentui-arrow-circle-down-20-o class="h-5 cursor-pointer hover:text-blue-500" />
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
                                    <x-fluentui-arrow-circle-down-20-o class="h-5 cursor-pointer hover:text-blue-500" />
                                </a>
                            </div>
                            <div class="ml-2">
                                {{ $auth->apikey }}
                            </div>
                        </div>
                    </div>



                    <div class="w-1/12">

                        <div class="flex flex-row">
                            <div>
                                <a href="#" wire:click="setTestSessionId('{{ $auth->sessionid }}')">
                                    <x-fluentui-arrow-circle-down-20-o class="h-5 cursor-pointer hover:text-blue-500" />
                                </a>
                            </div>
                            <div class="ml-2 truncate">
                                {{ $auth->sessionid }}
                            </div>
                        </div>
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


    <div class="mb-4 text-sm">
        <div class="flex flex-col w-11/12 m-auto border flatwhite pb-4">
            <div class="flex flex-row m-2 font-bold text-xl text-sky-600 justify-between">
                <div>
                    API - Beispielaufrufe
                </div>
                <div>
                    <a href="#" wire:click.prevent="neuSample" class="text-sky-500">
                        <div class="flex flex-row item-center">
                            <x-fluentui-note-add-16-o class="h-6" /> Neu
                        </div>
                    </a>
                </div>
            </div>

            <div class="flex flex-row ml-2">

                <div class="w-[50%] border-b">
                    Bezeichnung
                </div>
                <div class="w-[50%] border-b">
                    Url
                </div>
            </div>

            @foreach ($apiSamples as $sample)
                <div class="flex flex-row ml-2">
                    <div class="w-[50%]">
                        <div class="flex flex-row">
                            <div>
                                <a href="#" wire:click="editSample({{ $sample->id }})"
                                    title="Beispiel - Bearbeiten">
                                    <x-fluentui-arrow-circle-down-20-o class="h-5 cursor-pointer hover:text-blue-500" />
                                </a>
                            </div>
                            <div class="ml-2">
                                {{ $sample->bezeichnung }}
                            </div>
                        </div>
                    </div>
                    <div class="w-[50%]" x-data>
                        <div class="flex flex-row">
                            <div>
                                <a href="#" wire:click="setApiKey('{{ $sample->url }}')">
                                    <x-fluentui-arrow-circle-down-20-o class="h-5 cursor-pointer hover:text-blue-500" />
                                </a>
                            </div>
                            <div class="ml-2">
                                {{ $sample->url }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>


    <div class="w-full relative z-50">
        <div class="flex fixed top-0 bg-opacity-60 item-center w-full h-full bg-slate-100 backdrop-blur-[2px]"
            x-show="showApiEdit" x-on:click.self="showApiEdit = false" x-on:keydown.escape.window="showApiEdit = false">
            <div class="w-10/12 m-auto flatwhite">
                <div class="flex flex-row m-2 font-bold text-xl text-sky-600">
                    Eintrag ändern oder anlegen
                </div>
                <form wire:submit.prevent="save" class="w-full m-4">
                    <div class="flex flex-row items-center ">
                        <div class="w-[20%] mr-2">
                            <label for="appName" class="block font-bold">Applikationsname:</label>
                        </div>
                        <div class="w-[20%] mr-2">
                            <label for="apiKey" class="block font-bold">API-Key:</label>
                        </div>
                        <div class="w-[30%] mr-2">
                            <label for="edSessionId" class="block font-bold">Session-ID:</label>
                        </div>
                        <div class="w-[10%] mr-2">
                            <label for="sessionexpiry" class="block font-bold">Session-Ende:</label>
                        </div>
                        <div class="w-[10%] mr-2">
                            <label for="status" class="block font-bold">Status:</label>
                        </div>
                        <div class="w-[10%] mr-2">
                            &nbsp;
                        </div>


                    </div>
                    <div class="flex flex-row items-center  w-[97%]">
                        <div class="w-[20%] pr-2">
                            <input type="hidden" id="id" wire:model="id">
                            <input type="text" id="appName" wire:model="appName"
                                class="border rounded p-1 w-full">
                        </div>

                        <div class="w-[20%] pr-2">
                            <input type="text" id="apiKey" wire:model="apiKey"
                                class="border rounded p-1 w-full">
                        </div>
                        <div class="w-[30%] pr-2">
                            <input type="text" id="edSessionId" wire:model="edSessionId"
                                class="border rounded p-1 w-full">
                        </div>
                        <div class="w-[10%] pr-2">
                            <input type="datetime-local" id="sessionexpiry" wire:model="sessionexpiry"
                                class="border rounded p-1 w-full">
                        </div>
                        <div class="w-[10%] pr-2">
                            <input type="text" id="status" wire:model="status"
                                class="border rounded p-1 w-full" list="statuslist">
                            <datalist id="statuslist">
                                <option value="active">
                                <option value="inactive">
                                <option value="revoked">
                            </datalist>
                        </div>
                        <div class="w-[10%] items-center ">
                            <button type="submit"
                                class="bg-sky-600 text-white px-4 py-2 rounded  w-full">Speichern</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- API BEISPIELE ************************************************************************* -->
    <div class="w-full relative z-50">
        <div class="flex fixed top-0 bg-opacity-60 item-center w-full h-full bg-slate-100 backdrop-blur-[2px]"
            x-show="showApiSampleEdit" x-on:click.self="showApiSampleEdit = false"
            x-on:keydown.escape.window="showApiSampleEdit = false">
            <div class="w-8/12 m-auto flatwhite">
                <div class="flex flex-row m-2 font-bold text-xl text-sky-600">
                    API-Beispiel {{ $apiSampleEdit === true ? 'ändern' : 'anlegen' }}
                </div>
                <form wire:submit.prevent="saveSample" class="w-full m-4">
                    <div class="flex flex-row items-center w-[97%] mb-2">
                        <div class="mr-2 w-48">Bezeichnung:</div>
                        <div class="w-[50%] mr-4">
                            <input type="hidden" wire:model="apiSampleId">
                            <input type="text" id="apiSampleBezeichnung" wire:model="apiSampleBezeichnung" class="border rounded p-1 w-full">

                        </div>
                        <button type="submit" class="bg-sky-600 text-white px-4 py-2 rounded  w-40">
                            Speichern
                        </button>
                    </div>
                    <div class="flex flex-row items-center w-[97%] mb-2">
                        <div class="mr-2 w-48">Url:</div>
                        <div class="w-[50%] mr-4">
                            <input type="text" id="apiSampleUrl" wire:model="apiSampleUrl" class="border rounded p-1 w-full">
                        </div>

                    </div>



                    <div class="mb-2 flex flex-row items-center w-[97%] mb-2">
                        <div class="mr-2 w-48">Data:</div>
                        <div class="w-[50%] mr-4">

                            <textarea id="apiSampleData" wire:model="apiSampleData" class="border rounded p-1 h-80 w-full"></textarea>

                        </div>
                        <button type="button" class="bg-red-400 text-white px-4 py-2 rounded  w-40" wire:click="deleteSample">
                            Löschen
                        </button>
                    </div>




                </form>
            </div>
        </div>
    </div>

    <div class="w-11/12 m-auto flatwhite mb-4">
        <div>
            <div class="flex flex-row m-2 font-bold text-xl text-sky-600">
                Token erstellen aus Aufruf und API-Key
            </div>
            <form class="w-full m-4">
                <div class="mb-2 flex flex-row items-center ">
                    <div class="mr-2 w-48">API-Key:</div>
                    <div class="w-[50%]">
                        <input type="text" id="testApiKey" wire:model="testApiKey"
                            class="border rounded p-1 w-full">
                    </div>
                </div>
                <div class="mb-2 flex flex-row items-center ">
                    <div class="mr-2 w-48">Aufruf:</div>
                    <div class="w-[50%]">
                        <input type="text" id="aufruf" wire:model="aufruf" class="border rounded p-1 w-full">
                    </div>
                </div>
                <div class="mb-2 flex flex-row items-center ">
                    <div class="mr-2 w-48">API-Key Hash:</div>
                    <div class="w-[50%]">
                        <input type="text" id="testApiKeyHash" wire:model="testApiKeyHash" disabled
                            class="border rounded p-1 w-full bg-gray-200">
                    </div>
                </div>
                <div class="mb-2 flex flex-row items-center ">
                    <div class="mr-2 w-48">Token:</div>
                    <div class="w-[50%] mr-4">
                        <input type="text" id="token" wire:model="token" disabled
                            class="border rounded p-1 w-full bg-gray-200">
                    </div>
                    <div class="w-1/6 items-center">
                        <button type="button" wire:click="buildToken"
                            class="bg-sky-600 text-white px-4 py-2 rounded ">Build-Token</button>
                    </div>
                </div>
                <div class="mb-2 flex flex-row items-center ">
                    <div class="mr-2 w-48">Session-Id:</div>
                    <div class="w-[50%]">
                        <input type="text" id="testSessionId" wire:model="testSessionId" disabled
                            class="border rounded p-1 w-full bg-gray-200">
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
            <form class="w-full m-4">
                <div class="mb-2 flex flex-row items-center ">
                    <div class="mr-2 w-48">URL:</div>
                    <div class="w-[50%] mr-4">
                        <input type="text" id="testUrl" wire:model="testUrl" class="border rounded p-1 w-full">
                    </div>

                    <div class="w-4/12 items-center flex flex-row">
                        <button type="button" wire:click="funcTestUrl"
                            class="bg-sky-600 text-white px-4 py-2 rounded mr-6">Ausführen</button>
                        <div class="text-xl text-red-500 ">{{ $statusMessage }}</div>
                    </div>
                </div>

                <div class="mb-2 flex flex-row items-center ">
                    <div class="mr-2 w-48">Ergebnis:</div>
                    <div class="w-[50%] mr-4">

                        <textarea id="testResult" wire:model="testResult" class="border rounded p-1 h-80 w-full"></textarea>

                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
