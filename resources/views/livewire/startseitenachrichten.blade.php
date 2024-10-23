<div>
    @php
        use Illuminate\Support\Facades\Auth;
    @endphp
    @foreach ($nachrichten as $nachricht)
        <div class="m-4 p-3 flatwhite">
            <div class="flex flex-row  border-b border-slate-300  h-7">
                @if ($nachricht->prioritaet === 'hoch')
                    <span class="text-red-600"><x-fluentui-important-12-o class="h-6" /></span>
                @endif
                <div  id="id{{ $nachricht->id }}" class="font-bold text-base pb-2 text-sky-600
                    @if ($nachricht->prioritaet === 'mittel')
                    @elseif ($nachricht->prioritaet === 'hoch')
                    @else
                    <!-- normal -->
                    @endif">
                    {{ $nachricht->kurztext }}
                </div>
                @if ($nachricht->prioritaet === 'hoch')
                    <span class="text-red-600"><x-fluentui-important-12-o class="h-6" /></span>
                @endif

            </div>
            <div class="text-base">
                {{ $nachricht->langtext }}
            </div>
                @if (!empty($nachricht->links))
                <div class="text-base pt-2">

                    Links: <span class="text-ewe-gruen" ><a href="{{ $nachricht->links }}" target="_blank">{{ $nachricht->links }}</a></span>
                </div>
                @endif
        </div>
    @endforeach

    @if ( !empty(auth::user()) && auth::user()->isAdmin())
    <div class="m-4 p-3 flatwhite">
        <a href="{{ route('nachrichten') }}">Nachrichten verwalten</a>
    </div>
    @endif
</div>
