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
    <header class="w-full bg-slate-950 text-white border-b border-white/10 py-4 px-6">
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

                <a href="/" wire:navigate class="text-xs uppercase tracking-widest text-stone-400 hover:text-amber-400 flex items-center gap-1.5 transition-colors font-medium">
                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>{{ __('Inicio') }}</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col items-center justify-center px-4 sm:px-6 py-12">
        <div class="w-full sm:max-w-xl bg-white rounded-2xl shadow-2xl border-t-4 border-amber-500 border-x border-b border-stone-200 p-8 sm:p-12 relative overflow-hidden animate-fade-in-up">
            {{ $slot }}
        </div>

        <!-- Trust Security Badge -->
        <div class="mt-8 flex items-center justify-center gap-2 text-xs text-stone-500 font-medium">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            <span>{{ __('100% Confidencial y Seguro') }}</span>
        </div>
    </main>

    <!-- Bottom Footer Note -->
    <footer class="py-6 text-center text-xs text-stone-500 border-t border-stone-200">
        <p>&copy; {{ date('Y') }} {{ __('Portal de Oración') }}. {{ __('Unidos en Fe y Oración') }}.</p>
    </footer>

    @livewireScripts
</body>
</html>
