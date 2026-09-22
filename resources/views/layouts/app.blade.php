<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Portal de Oración') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-soul-indigo bg-white selection:bg-soul-gold/30 selection:text-soul-indigo">
        
        <div class="fixed inset-0 z-0 bg-soul-cream pointer-events-none">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-soul-gold/10 via-white to-white"></div>
        </div>

        <div class="relative z-10 min-h-screen flex flex-col">
            
            <!-- Top Navigation -->
            <div class="w-full shadow-sm relative z-20">
                <livewire:layout.navigation />
            </div>

            <div class="flex-1 flex flex-col w-full">
                <!-- Page Heading -->
                @if (isset($header))
                    <header class="bg-transparent pt-8 pb-4 px-4 sm:px-6 lg:px-8">
                        <div class="max-w-5xl mx-auto">
                            <div class="font-serif text-3xl text-soul-indigo">{{ $header }}</div>
                        </div>
                    </header>
                @endif

                <!-- Page Content -->
                <main class="flex-1 px-4 sm:px-6 lg:px-8 pb-12">
                    <div class="max-w-5xl mx-auto mt-4">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
