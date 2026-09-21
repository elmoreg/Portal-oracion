<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Portal de Oración') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        <div class="min-h-screen flex flex-col">
            <header class="max-w-5xl mx-auto w-full px-6 py-6 flex items-center justify-between">
                <span class="font-semibold text-lg">{{ config('app.name', 'Portal de Oración') }}</span>
                <nav class="text-sm space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" wire:navigate class="text-indigo-600">Ir a mi panel</a>
                    @else
                        <a href="{{ route('login') }}" wire:navigate class="text-gray-600">Iniciar sesión</a>
                        <a href="{{ route('register') }}" wire:navigate class="text-indigo-600 font-medium">Inscribirme para orar</a>
                    @endauth
                </nav>
            </header>

            <main class="flex-1 flex items-center">
                <div class="max-w-5xl mx-auto w-full px-6 py-12 grid md:grid-cols-2 gap-8">
                    <a href="{{ route('prayer.create') }}" wire:navigate
                       class="block bg-white rounded-xl shadow-sm border border-gray-100 p-8 hover:border-indigo-300 transition">
                        <h2 class="text-xl font-semibold mb-2">Pedir oración</h2>
                        <p class="text-gray-600 text-sm">
                            Compartí tu petición de forma anónima. Vas a recibir un enlace único y privado
                            para hacerle seguimiento y conversar con quien ore por vos, si lo necesitás.
                        </p>
                        <span class="inline-block mt-4 text-indigo-600 text-sm font-medium">Enviar una petición &rarr;</span>
                    </a>

                    <a href="{{ route('register') }}" wire:navigate
                       class="block bg-white rounded-xl shadow-sm border border-gray-100 p-8 hover:border-indigo-300 transition">
                        <h2 class="text-xl font-semibold mb-2">Orar por otros</h2>
                        <p class="text-gray-600 text-sm">
                            Inscribite como intercesor. El equipo del portal te va a asignar peticiones de
                            oración para que las acompañes.
                        </p>
                        <span class="inline-block mt-4 text-indigo-600 text-sm font-medium">Inscribirme &rarr;</span>
                    </a>
                </div>
            </main>
        </div>
    </body>
</html>
