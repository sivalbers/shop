<div class="w-11/12 m-auto flex flex-col h-full">
    <!-- Fixierter Header-Bereich -->
    <div class="p-3 flatwhite flex flex-row items-center justify-between mb-2">
        <div class="text-xl font-bold text-sky-600 border-b border-sky-600">
            Log-Datei
        </div>
        <div>
            <button type="button"
                    x-data @click="if (confirm('Log-Datei wirklich leeren?')) { $wire.clearLogs() }"
                    class="mt-2 px-4 py-2 bg-red-600 text-white rounded-md flex flex-row items-center shadow-md shadow-gray-400">
                <x-fluentui-delete-28-o class="h-6 pr-2" />Log leeren
            </button>
        </div>
    </div>

    @if(session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Flexibles Layout für den Log-Inhalt -->
    <div class="flex-1 min-h-0 flex flex-col flatwhite">
        <!-- Der Inhalt nimmt den verbleibenden Platz ein -->
        <div class="flex-1 min-h-0 overflow-y-auto">
            <pre class="overflow-x-auto whitespace-pre-wrap p-4">
@foreach($logLines as $line)
{{ $line }}
@endforeach
            </pre>
        </div>
    </div>
</div>
