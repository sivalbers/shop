<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- Components/layouts/app -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>   
    <body class="font-sans antialiased flex flex-col h-screen">
        <!-- Fixiere den Header oben -->
        <div class="top-0 left-0 w-full z-50">
            <x-zheader />
            <!-- Navigation bleibt oben fixiert -->
            <div class="bg-white shadow ">
                @livewire('NavigationComponent')
            </div>
        </div>

        <div class="flex-1 flex flex-col py-2 bg-gray-100 dark:bg-gray-900 overflow-auto">
            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">


                    {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="flex-1 min-h-0 overflow-auto">
                {{ $slot }}
            </main>
        </div>

        <x-zfooter />
        <script>
            function forceLightboxInitialization() {
                if (typeof lightbox !== "undefined") {
                    lightbox.init();

                    // Setze `data-lightbox-initialized`, um doppelte Initialisierungen zu vermeiden
                    document.querySelectorAll("[data-lightbox]").forEach((el) => {
                        el.setAttribute("data-lightbox-initialized", "true");
                    });
                }
            }

            document.addEventListener("DOMContentLoaded", function () {
                setTimeout(forceLightboxInitialization, 100);
            });

            if (!window.checkLivewire) {
                window.checkLivewire = setInterval(() => {
                    if (typeof Livewire !== "undefined") {
                        clearInterval(window.checkLivewire);

                        document.addEventListener("livewire:load", function () {
                            setTimeout(forceLightboxInitialization, 100);
                        });

                        Livewire.hook("message.processed", () => {
                            setTimeout(forceLightboxInitialization, 100);
                        });
                    }
                }, 200);
            }

            // Lightbox beim ersten Klick sofort aktivieren
            document.addEventListener("click", function (event) {
                let target = event.target.closest("a[data-lightbox]");
                if (target && !target.hasAttribute("data-lightbox-initialized")) {
                    forceLightboxInitialization();
                    event.preventDefault();
                    setTimeout(() => {
                        target.click();
                    }, 100);
                }
            });
        </script>











    </body>
</html>
