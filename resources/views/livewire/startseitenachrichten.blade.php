<div>


    <div class="mx-auto sm:px-6 lg:px-8 w-4/5">

        <div class="w-full text-xs">
            @php
                use Illuminate\Support\Facades\Auth;
            @endphp
            <div class="text-3xl text-sky-600  font-bold">
                Nachrichten
            </div>
            @foreach ($nachrichten as $nachricht)
                <div class="flex flex-row m-2 p-3 flatwhite items-center">

                    <div class="min-w-12   flex justify-center "> <!-- Spalte 1 -->
                        @if ($nachricht->prioritaet === 'hoch')
                            <span class="text-red-600"><x-fluentui-important-24 class="h-12" /></span>
                        @elseif ($nachricht->prioritaet === 'mittel')
                            <span class="text-ewe-gruen "><x-fluentui-important-24-o class="h-12" /></span>
                        @else

                        @endif
                    </div>

                    <div class="flex flex-col  w-full"><!-- Spalte 2 -->
                        <div class="flex flex-row items-center justify-between">
                            <div class="text-xl text-sky-600 font-bold">
                                {{ $nachricht->kurztext }}
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $nachricht->updated_at->format('d.m.Y h:m') }}
                            </div>
                        </div>
                        <div class="text-base">
                            {{ $nachricht->langtext }}
                        </div>
                        @if (!empty($nachricht->links))
                            <div class="text-base pt-2">
                                @php
                                    $links = $nachricht->getLinksArray();
                                @endphp
                                @if (count($links) > 1)
                                    Links:
                                    @foreach ( $links as $link )
                                        <div class="flex flex-col">
                                            <div class="flex flex-row">
                                                <div class="min-w-4">
                                                </div>

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


        @if (!empty(auth::user()) && auth::user()->isAdmin())
        <a href="{{ route('nachrichten') }}">
            <div class="flex flex-row m-2 p-3 flatwhite bg-green-100 text-ewe-gruen items-center">
                <div class="min-w-12  flex justify-center"> <!-- Spalte 1 -->
                    <x-fluentui-calligraphy-pen-20-o class="h-8 mr-2" />
                </div>
                <div class="">
                    Nachrichten verwalten
                </div>

            </div>
        </a>
        @endif
    </div>
</div>


