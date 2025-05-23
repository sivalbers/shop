<div>
    <div class="flex flex-row">
        <div class="flex-1 m-4"><a href="{{ route('datenschutz') }}">Datenschutz</a></div>
        <div class="flex-1 m-4"><a href="{{ route('impressum') }}">{{ trans('auth.Impressum') }}</a></div>
        @if ( auth() && auth()->user() && auth()->user()->isAdmin())
        <div class="flex-1 m-4"><a href="https://blade-ui-kit.com/blade-icons?set=43" target="_blank">icons</a></div>
        @endif
    </div>
</div>
