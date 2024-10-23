<div>
    <div class="flex flex-row">
        <div class="flex-1 m-4">Datenschutz</div>
        <div class="flex-1 m-4">{{ trans('auth.Impressum') }}</div>
        @if ( auth() && auth()->user() && auth()->user()->isAdmin())
        <div class="flex-1 m-4"><a href="https://blade-ui-kit.com/blade-icons?set=43" traget="_blank">icons</a></div>
        @endif
    </div>
</div>
