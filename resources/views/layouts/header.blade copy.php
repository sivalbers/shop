<div class="border-b border-gray-300 shadow-inner-bottom shadow-[rgb(200,212,0) bg-[rgb(200,212,0)]">
    <div class="flex flex-auto m-auto text-center">
        <div class="w-full  ">
            <div class="text-base font-light text-[rgb(0,119,180)]">

                @php
                    $s = '';
                    if (count($nachrichten) > 0){
                        $s = '+++ ';
                        foreach ($nachrichten as $key => $nachricht) {
                            //$s = sprintf( '%s <a href="%s#id%d"><span class="prio_%s">%s</span></a> +++', $s, route('startseite'), $nachricht->id, $nachricht->prioritaet, $nachricht->kurztext);
                           $s = sprintf( '%s <button wire:click="nachrichtClick(%d)"> <span class="prio_%s">%s</span></button> +++', $s, $nachricht->id, $nachricht->prioritaet, $nachricht->kurztext);

                        }
                    }
                @endphp
                {!! $s !!}

            </div>
        </div>
    </div>
</div>

