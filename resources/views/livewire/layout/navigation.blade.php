<?php

use App\Enums\PrayerRequestStatus;
use App\Livewire\Actions\Logout;
use App\Models\PrayerRequest;
use Livewire\Volt\Component;

new class extends Component
{
    public function getPendingCountProperty(): int
    {
        return PrayerRequest::where('status', PrayerRequestStatus::Pending)->count();
    }

    public function getAssignedCountProperty(): int
    {
        if (! auth()->check()) {
            return 0;
        }

        return auth()->user()->assignedPrayerRequests()->count();
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
       class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-950 text-white flex flex-col justify-between transition-transform duration-300 ease-in-out border-r border-white/10 shadow-2xl">
    
    <!-- Top Section: Brand & Navigation -->
    <div class="flex-1 flex flex-col overflow-y-auto">
        
        <!-- Brand / Logo (TailAdmin style) -->
        <div class="h-20 flex items-center justify-between px-6 border-b border-white/10 shrink-0">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center font-bold text-lg shadow-md shadow-amber-500/20 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 3v18M6 9h12"></path></svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-bold tracking-wider text-white uppercase group-hover:text-amber-400 transition-colors">
                        {{ __(config('app.name', 'Portal de Oración')) }}
                    </span>
                    <span class="text-[10px] text-amber-400 font-bold uppercase tracking-widest">
                        {{ auth()->user()->isAdmin() ? __('Panel Administrador') : __('Panel Intercesor') }}
                    </span>
                </div>
            </a>

            <!-- Mobile Close Button -->
            <button @click="sidebarOpen = false" class="lg:hidden text-stone-400 hover:text-white p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Navigation Menu Groups -->
        <div class="px-4 py-6 space-y-6">
            
            <!-- Group: Administración (if admin) -->
            @if(auth()->user()->isAdmin())
                <div>
                    <h3 class="px-3 text-[11px] font-bold uppercase tracking-[0.2em] text-stone-400 mb-2">
                        {{ __('Gestión & Control') }}
                    </h3>
                    <nav class="space-y-1">
                        
                        <!-- Dashboard Link -->
                        <a href="{{ route('admin.dashboard') }}" wire:navigate 
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'text-stone-300 hover:text-white hover:bg-white/5' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.dashboard') ? 'text-slate-950' : 'text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                <span>{{ __('Panel Principal') }}</span>
                            </div>
                        </a>

                        <!-- Peticiones Link with Badge -->
                        <a href="{{ route('admin.prayer-requests.index') }}" wire:navigate 
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 {{ request()->routeIs('admin.prayer-requests.*') ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'text-stone-300 hover:text-white hover:bg-white/5' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.prayer-requests.*') ? 'text-slate-950' : 'text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                <span>{{ __('Peticiones de Oración') }}</span>
                            </div>
                            @if($this->pendingCount > 0)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ request()->routeIs('admin.prayer-requests.*') ? 'bg-slate-950 text-amber-400' : 'bg-amber-500 text-slate-950' }}">
                                    {{ $this->pendingCount }}
                                </span>
                            @endif
                        </a>

                        <!-- Intercessors Link -->
                        <a href="{{ route('admin.intercessors.index') }}" wire:navigate 
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 {{ request()->routeIs('admin.intercessors.*') ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'text-stone-300 hover:text-white hover:bg-white/5' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.intercessors.*') ? 'text-slate-950' : 'text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <span>{{ __('Equipo de Intercesores') }}</span>
                            </div>
                        </a>

                        <!-- Users Link -->
                        <a href="{{ route('admin.users.index') }}" wire:navigate 
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'text-stone-300 hover:text-white hover:bg-white/5' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.users.*') ? 'text-slate-950' : 'text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                <span>{{ __('Cuentas & Usuarios') }}</span>
                            </div>
                        </a>

                    </nav>
                </div>
            @endif

            <!-- Group: Intercesión (if intercessor) -->
            @if(auth()->user()->isIntercessor())
                <div>
                    <h3 class="px-3 text-[11px] font-bold uppercase tracking-[0.2em] text-stone-400 mb-2">
                        {{ __('Ministerio de Oración') }}
                    </h3>
                    <nav class="space-y-1">
                        
                        <!-- Intercessor Dashboard -->
                        <a href="{{ route('intercessor.dashboard') }}" wire:navigate 
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 {{ request()->routeIs('intercessor.dashboard') ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'text-stone-300 hover:text-white hover:bg-white/5' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 {{ request()->routeIs('intercessor.dashboard') ? 'text-slate-950' : 'text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                <span>{{ __('Mis Peticiones Asignadas') }}</span>
                            </div>
                            @if($this->assignedCount > 0)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ request()->routeIs('intercessor.dashboard') ? 'bg-slate-950 text-amber-400' : 'bg-amber-500 text-slate-950' }}">
                                    {{ $this->assignedCount }}
                                </span>
                            @endif
                        </a>

                    </nav>
                </div>
            @endif

            <!-- Group: Accesos Rápidos -->
            <div>
                <h3 class="px-3 text-[11px] font-bold uppercase tracking-[0.2em] text-stone-400 mb-2">
                    {{ __('Accesos Rápidos') }}
                </h3>
                <nav class="space-y-1">
                    
                    <a href="{{ route('prayer.muro') }}" wire:navigate 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold text-stone-300 hover:text-white hover:bg-white/5 transition-all">
                        <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                        <span>{{ __('Muro de Oración') }}</span>
                    </a>

                    <a href="{{ route('prayer.create') }}" wire:navigate 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold text-stone-300 hover:text-white hover:bg-white/5 transition-all">
                        <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ __('Nueva Petición') }}</span>
                    </a>

                    <a href="/" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold text-stone-300 hover:text-white hover:bg-white/5 transition-all">
                        <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        <span>{{ __('Ir a la Página de Inicio') }}</span>
                    </a>

                </nav>
            </div>

        </div>

    </div>

    <!-- Bottom User Section (TailAdmin Style) -->
    <div class="p-4 border-t border-white/10 shrink-0 bg-slate-900/50">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-xl bg-amber-500 text-slate-950 font-bold text-xs flex items-center justify-center shrink-0 shadow-xs">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-stone-400 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>

            <button wire:click="logout" 
                    title="{{ __('Cerrar Sesión') }}"
                    class="p-2 text-stone-400 hover:text-red-400 hover:bg-white/5 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </button>
        </div>
    </div>

</aside>
