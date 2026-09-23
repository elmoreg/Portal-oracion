<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __(config('app.name', 'Portal de Oración')) }} - {{ __('Panel') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased text-slate-800 bg-stone-100 selection:bg-amber-500 selection:text-white"
      x-data="{ sidebarOpen: false }">

    <div class="min-h-screen flex">
        
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-slate-950/60 z-40 lg:hidden"
             style="display: none;"></div>

        <!-- TailAdmin Sidebar Component -->
        <livewire:layout.navigation />

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 lg:pl-72 transition-all duration-300">
            
            <!-- Topbar Header (TailAdmin Style) -->
            <header class="sticky top-0 z-30 bg-white border-b border-stone-200/80 shadow-xs h-18 flex items-center justify-between px-4 sm:px-6 lg:px-8">
                
                <!-- Left: Mobile Toggle & Quick Search -->
                <div class="flex items-center gap-4 flex-1 max-w-lg">
                    <!-- Hamburger Button for Mobile & Desktop -->
                    <button @click="sidebarOpen = !sidebarOpen" 
                            class="p-2 rounded-lg text-stone-500 hover:text-slate-900 hover:bg-stone-100 lg:hidden focus:outline-none focus:ring-2 focus:ring-amber-500"
                            aria-label="Toggle Menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>

                    <!-- Search Input (TailAdmin Style) -->
                    <div class="relative w-full hidden sm:block">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-stone-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="text" 
                               placeholder="{{ __('Buscar en el portal...') }}" 
                               class="w-full bg-stone-50 text-slate-800 text-xs rounded-xl border border-stone-200 pl-9 pr-10 py-2.5 focus:bg-white focus:border-amber-500 focus:ring-amber-500 transition-all placeholder-stone-400">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-2.5">
                            <kbd class="px-1.5 py-0.5 text-[10px] font-semibold text-stone-400 bg-white border border-stone-200 rounded">⌘K</kbd>
                        </span>
                    </div>
                </div>

                <!-- Right: Role Badge, Language, User Dropdown -->
                <div class="flex items-center gap-4">
                    
                    <!-- Role Pill -->
                    @if(auth()->user()->isAdmin())
                        <span class="hidden md:inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-700 text-xs font-bold uppercase tracking-wider">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            {{ __('Administrador') }}
                        </span>
                    @elseif(auth()->user()->isIntercessor())
                        <span class="hidden md:inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/30 text-blue-700 text-xs font-bold uppercase tracking-wider">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            {{ __('Intercesor') }}
                        </span>
                    @endif

                    <x-language-selector variant="light" />

                    <!-- User Profile Dropdown -->
                    <x-dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-3 p-1 rounded-xl hover:bg-stone-50 transition-colors focus:outline-none">
                                <div class="w-9 h-9 rounded-xl bg-slate-950 text-amber-400 font-bold text-sm flex items-center justify-center shadow-xs border border-white/10">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <div class="hidden md:flex flex-col text-left">
                                    <span class="text-xs font-bold text-slate-900 leading-tight">{{ auth()->user()->name }}</span>
                                    <span class="text-[11px] text-stone-500 font-medium">{{ auth()->user()->email }}</span>
                                </div>
                                <svg class="w-4 h-4 text-stone-400 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-3 border-b border-stone-100">
                                <p class="text-xs text-stone-400">{{ __('Conectado como') }}</p>
                                <p class="text-sm font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                            </div>

                            <x-dropdown-link :href="route('profile')" wire:navigate class="flex items-center gap-2 text-xs font-medium">
                                <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                <span>{{ __('Mi Perfil') }}</span>
                            </x-dropdown-link>

                            <x-dropdown-link href="/" class="flex items-center gap-2 text-xs font-medium">
                                <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                <span>{{ __('Ver Sitio Web') }}</span>
                            </x-dropdown-link>

                            <div class="border-t border-stone-100 my-1"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-xs font-bold text-red-600 hover:bg-red-50 flex items-center gap-2 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    <span>{{ __('Cerrar Sesión') }}</span>
                                </button>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </header>

            <!-- Page Canvas Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                
                <!-- Page Breadcrumbs Header -->
                @if (isset($header))
                    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
                                {{ $header }}
                            </h1>
                            <p class="text-xs text-stone-500 font-medium mt-1">
                                {{ __('Gestión y acompañamiento del Portal de Oración') }}
                            </p>
                        </div>
                    </div>
                @endif

                {{ $slot }}
            </main>

            <!-- Panel Footer -->
            <footer class="py-4 px-8 text-center text-xs text-stone-400 border-t border-stone-200/80 bg-white">
                <p>&copy; {{ date('Y') }} {{ __(config('app.name', 'Portal de Oración')) }}. {{ __('Panel de Control y Gestión.') }}</p>
            </footer>

        </div>

    </div>

</body>
</html>
