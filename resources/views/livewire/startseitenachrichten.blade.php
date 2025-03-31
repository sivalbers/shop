<div class="flex flex-col lg:flex-row items-start w-full">
    @php
        use Illuminate\Support\Facades\Auth;
    @endphp
    <!-- Nachrichtenübersicht -->
    <div class="w-[95%] lg:sticky lg:top-[3px] lg:w-[22%] mx-auto flatwhite z-40 shadow-md  p-2">
        <div class="text-sky-600 text-xl border-b border-sky-600 font-bold py-1">
            Nachrichtenübersicht
        </div>
        @foreach ($nachrichten as $nachricht)
            <a href="#nachricht{{ $nachricht->id }}" class="">
                <div class="flex flex-row text-sm border-b hover:bg-ewe-ltgruen py-1">
                    <div class="flex justify-center">
                        @if ($nachricht->prioritaet === 'hoch')
                            <span class="text-red-600"><x-fluentui-important-24 class="h-5" /></span>
                        @elseif ($nachricht->prioritaet === 'mittel')
                            <span class="text-ewe-gruen"><x-fluentui-important-24-o class="h-5" /></span>
                        @else
                            <span class="text-white"><x-fluentui-tab-16-o class="h-5" /></span>
                        @endif
                    </div>
                    <div>
                        {{ $nachricht->kurztext }}
                    </div>
                </div>
            </a>
        @endforeach
        @if (!empty(auth::user()) && auth::user()->isReporter())
            <a href="{{ route('nachrichten') }}">
                <div class="flex flex-row text-ewe-gruen items-center">
                    <div class="min-w-12 flex justify-center">
                        <x-fluentui-calligraphy-pen-20-o class="h-8 mr-2" />
                    </div>
                    <div>Nachrichten verwalten</div>
                </div>
            </a>
        @endif
    </div>

    <!-- Nachrichtenbereich -->
    <div class="w-full lg:w-[78%] px-4">
        <div class="w-full text-xs">

            <div class="text-3xl text-sky-600 font-bold">
                Nachrichten
            </div>
            @foreach ($nachrichten as $nachricht)
                <div class="flex flex-row m-2 p-3 flatwhite items-center" id="nachricht{{ $nachricht->id }}">
                    <div class="min-w-12 flex justify-center">
                        @if ($nachricht->prioritaet === 'hoch')
                            <span class="text-red-600"><x-fluentui-important-24 class="h-12" /></span>
                        @elseif ($nachricht->prioritaet === 'mittel')
                            <span class="text-ewe-gruen"><x-fluentui-important-24-o class="h-12" /></span>
                        @endif
                    </div>

                    <div class="flex flex-col w-full">
                        <div class="flex flex-row items-center justify-between">
                            <div class="text-xl text-sky-600 font-bold ">
                                {{ $nachricht->kurztext }}
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $nachricht->updated_at->diffForHumans() }}
                            </div>
                        </div>
                        <div class="text-base">
                            {!! nl2br(e($nachricht->langtext)) !!}
                        </div>

                        @if (!empty($nachricht->links))
                            <div class="text-base pt-2">
                                @php
                                    $links = $nachricht->getLinksArray();
                                @endphp
                                @if (count($links) > 1)
                                    Links:
                                    @foreach ($links as $link)
                                        <div class="flex flex-col">
                                            <div class="flex flex-row">
                                                <div class="min-w-4"></div>
                                                <div>
                                                    <span class="text-ewe-gruen">
                                                        <a href="{{ $link['link'] }}" target="_blank">{{ $link['beschreibung'] }}</a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    Link: <span class="text-ewe-gruen">
                                        <a href="{{ $links[0]['link'] }}" target="_blank">{{ $links[0]['beschreibung'] }}</a>
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Nachrichten verwalten Button -->
        @if (!empty(auth::user()) && auth::user()->isReporter())
            <a href="{{ route('nachrichten') }}">
                <div class="flex flex-row m-2 p-3 flatwhite bg-green-100 text-ewe-gruen items-center">
                    <div class="min-w-12 flex justify-center">
                        <x-fluentui-calligraphy-pen-20-o class="h-8 mr-2" />
                    </div>
                    <div>Nachrichten verwalten</div>
                </div>
            </a>
        @endif
    </div>
</div>
