<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __(config('app.name', 'Portal de Oración')) }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans text-slate-800 antialiased bg-stone-100 selection:bg-amber-500 selection:text-white min-h-screen flex flex-col justify-between">
    
    <!-- Top Minimalist Brand Header -->
    <header class="w-full bg-slate-950 text-white border-b border-white/10 py-4 px-6 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <a href="/" wire:navigate class="flex items-center gap-3 group">
                <div class="w-9 h-9 rounded-full bg-amber-500 flex items-center justify-center text-slate-950 font-bold shadow-md shadow-amber-500/20 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 3v18M6 9h12"></path></svg>
                </div>
                <span class="tracking-wider text-base font-bold text-white group-hover:text-amber-400 transition-colors uppercase">
                    {{ __('Portal de Oración') }}
                </span>
            </a>

            <div class="flex items-center gap-4 sm:gap-6">
                <x-language-selector variant="dark" />

                <a href="{{ route('prayer.create') }}" wire:navigate class="hidden sm:inline-flex px-4 py-2 rounded bg-amber-500 text-slate-950 font-bold text-xs uppercase tracking-wider hover:bg-amber-400 transition-all">
                    {{ __('Pedir Oración') }}
                </a>
                <a href="/" wire:navigate class="text-xs uppercase tracking-widest text-stone-400 hover:text-amber-400 flex items-center gap-1.5 transition-colors font-medium">
                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>{{ __('Inicio') }}</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
        {{ $slot }}
    </main>

    <!-- Bottom Footer Note -->
    <footer class="py-6 text-center text-xs text-stone-500 border-t border-stone-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; {{ date('Y') }} {{ __('Portal de Oración') }}. {{ __('Todos los derechos reservados.') }}</p>
            <p class="text-stone-400">{{ __('Confidencialidad absoluta y encriptación segura.') }}</p>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
