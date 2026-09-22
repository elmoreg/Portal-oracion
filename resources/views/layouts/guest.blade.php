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
    <body class="font-sans text-soul-indigo antialiased bg-gray-50 selection:bg-soul-gold/30 selection:text-soul-indigo">
        
        <!-- Subtle modern background -->
        <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob"></div>
            <div class="absolute top-[20%] -right-[10%] w-[50%] h-[50%] bg-soul-gold/10 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob" style="animation-delay: 2s;"></div>
            <div class="absolute -bottom-[20%] left-[20%] w-[50%] h-[50%] bg-soul-accent/10 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob" style="animation-delay: 4s;"></div>
        </div>

        <div class="relative z-10 min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="mb-8 mt-10 sm:mt-0 animate-fade-in-up">
                <a href="/" wire:navigate class="flex flex-col items-center gap-3 group">
                    <div class="w-14 h-14 bg-gradient-to-br from-soul-indigo to-blue-800 rounded-2xl flex items-center justify-center shadow-lg transform group-hover:scale-105 transition-transform">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v19M5 10h14"></path></svg>
                    </div>
                    <span class="font-bold text-3xl tracking-tight text-soul-indigo">{{ config('app.name', 'Portal de Oración') }}</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md px-8 py-10 bg-white shadow-2xl border border-gray-100 sm:rounded-[2rem] animate-fade-in-up" style="animation-delay: 0.1s;">
                {{ $slot }}
            </div>
            
            <div class="mt-8 mb-10 sm:mb-0 flex items-center justify-center gap-2 text-sm text-gray-500 font-medium animate-fade-in-up" style="animation-delay: 0.2s;">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Confidencialidad absoluta y encriptación segura.
            </div>
        </div>
    </body>
</html>
