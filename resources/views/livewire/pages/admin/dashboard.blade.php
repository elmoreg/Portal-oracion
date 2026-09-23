<?php

use App\Enums\PrayerRequestStatus;
use App\Models\PrayerRequest;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public function getStatsProperty(): array
    {
        return [
            'total' => PrayerRequest::count(),
            'pending' => PrayerRequest::where('status', PrayerRequestStatus::Pending)->count(),
            'praying' => PrayerRequest::where('status', PrayerRequestStatus::Praying)->count(),
            'answered' => PrayerRequest::where('status', PrayerRequestStatus::Answered)->count(),
        ];
    }

    public function getZonesProperty()
    {
        return PrayerRequest::query()
            ->whereNotNull('country_name')
            ->selectRaw('country_name, count(*) as total')
            ->groupBy('country_name')
            ->orderByDesc('total')
            ->limit(6)
            ->get();
    }

    public function getRecentProperty()
    {
        return PrayerRequest::where('status', PrayerRequestStatus::Pending)
            ->latest()
            ->limit(5)
            ->get();
    }
}; ?>

<div class="space-y-8">
    <x-slot name="header">
        {{ __('Panel Principal de Administración') }}
    </x-slot>

    <!-- Stat Cards Grid (TailAdmin Style) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Card 1: Total Peticiones -->
        <div class="bg-white rounded-2xl p-6 border border-stone-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-slate-950/5 text-slate-900 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <span class="text-xs font-bold text-stone-500 bg-stone-100 px-2.5 py-1 rounded-full">
                    {{ __('Histórico') }}
                </span>
            </div>
            <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">{{ __('Total Peticiones') }}</p>
            <div class="flex items-baseline justify-between">
                <h3 class="text-3xl font-extrabold text-slate-900">{{ $this->stats['total'] }}</h3>
                <span class="text-xs text-emerald-600 font-bold flex items-center gap-0.5">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                    {{ __('100% registradas') }}
                </span>
            </div>
        </div>

        <!-- Card 2: Sin Asignar (Pending) -->
        <div class="bg-white rounded-2xl p-6 border border-amber-500/30 shadow-xs hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="absolute top-0 right-0 w-2 h-full bg-amber-500"></div>
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-bold">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                    {{ __('Por Asignar') }}
                </span>
            </div>
            <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">{{ __('Peticiones Pendientes') }}</p>
            <div class="flex items-baseline justify-between">
                <h3 class="text-3xl font-extrabold text-slate-900">{{ $this->stats['pending'] }}</h3>
                <span class="text-xs text-amber-700 font-bold">
                    {{ __('Requiere atención') }}
                </span>
            </div>
        </div>

        <!-- Card 3: En Oración Activa -->
        <div class="bg-white rounded-2xl p-6 border border-stone-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </div>
                <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-full">
                    {{ __('En Proceso') }}
                </span>
            </div>
            <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">{{ __('En Oración Activa') }}</p>
            <div class="flex items-baseline justify-between">
                <h3 class="text-3xl font-extrabold text-slate-900">{{ $this->stats['praying'] }}</h3>
                <span class="text-xs text-blue-600 font-bold">
                    {{ __('Con intercesores') }}
                </span>
            </div>
        </div>

        <!-- Card 4: Contestadas / Testimonios -->
        <div class="bg-white rounded-2xl p-6 border border-stone-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full">
                    {{ __('Respondidas') }}
                </span>
            </div>
            <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">{{ __('Peticiones Contestadas') }}</p>
            <div class="flex items-baseline justify-between">
                <h3 class="text-3xl font-extrabold text-slate-900">{{ $this->stats['answered'] }}</h3>
                <span class="text-xs text-emerald-600 font-bold">
                    {{ __('Gloria a Dios') }}
                </span>
            </div>
        </div>

    </div>

    <!-- Main 2-Column Section (TailAdmin Widgets) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Peticiones por Zona Geográfica (7 cols) -->
        <div class="lg:col-span-6 bg-white rounded-2xl p-6 sm:p-8 border border-stone-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-6 border-b border-stone-100 pb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">{{ __('Distribución por País / Región') }}</h3>
                    <p class="text-xs text-stone-500">{{ __('Procedencia de las solicitudes registradas') }}</p>
                </div>
                <span class="text-xs font-bold text-stone-400">
                    {{ count($this->zones) }} {{ __('Zonas') }}
                </span>
            </div>

            <div class="space-y-5">
                @forelse ($this->zones as $zone)
                    @php
                        $percentage = $this->stats['total'] > 0 ? round(($zone->total / $this->stats['total']) * 100, 1) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between text-xs font-bold text-slate-800 mb-1.5">
                            <span class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                {{ $zone->country_name }}
                            </span>
                            <span class="text-stone-500 font-semibold">{{ $zone->total }} {{ __('peticiones') }} ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full bg-stone-100 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-gradient-to-r from-amber-500 to-amber-600 h-2.5 rounded-full transition-all duration-500" 
                                 style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-xs text-stone-400">
                        {{ __('Aún no hay datos geográficos disponibles.') }}
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Peticiones Recientes Pendientes de Asignar (6 cols) -->
        <div class="lg:col-span-6 bg-white rounded-2xl p-6 sm:p-8 border border-stone-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-6 border-b border-stone-100 pb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">{{ __('Últimas Peticiones Sin Asignar') }}</h3>
                        <p class="text-xs text-stone-500">{{ __('Peticiones en espera de intercesores') }}</p>
                    </div>
                    <a href="{{ route('admin.prayer-requests.index') }}" wire:navigate 
                       class="text-xs font-bold text-amber-600 hover:text-amber-500 transition-colors inline-flex items-center gap-1">
                        <span>{{ __('Ver todas') }}</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse ($this->recent as $prayerRequest)
                        <a href="{{ route('admin.prayer-requests.show', $prayerRequest) }}" wire:navigate 
                           class="group block p-4 rounded-xl border border-stone-200/80 hover:border-amber-400 bg-stone-50/50 hover:bg-white hover:shadow-xs transition-all">
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <span class="text-xs font-bold text-slate-900 group-hover:text-amber-600 transition-colors">
                                    {{ $prayerRequest->requester_name ?: __('Petición Anónima') }}
                                </span>
                                <span class="text-[11px] font-semibold text-stone-400">
                                    {{ $prayerRequest->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <p class="text-xs text-stone-600 line-clamp-2 italic font-serif leading-relaxed mb-2">
                                “{{ $prayerRequest->translated_content }}”
                            </p>
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="px-2 py-0.5 rounded bg-stone-200/70 text-stone-600 font-medium">
                                    {{ $prayerRequest->country_name ?? __('Zona desconocida') }}
                                </span>
                                <span class="text-xs font-bold text-amber-600 group-hover:translate-x-0.5 transition-transform inline-flex items-center gap-1">
                                    <span>{{ __('Asignar') }}</span> &rarr;
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-10">
                            <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <p class="text-xs font-bold text-slate-800">{{ __('Todo al día') }}</p>
                            <p class="text-[11px] text-stone-400 mt-0.5">{{ __('No hay peticiones pendientes de asignar.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    <!-- Quick Access Hub (TailAdmin style) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        
        <a href="{{ route('admin.prayer-requests.index') }}" wire:navigate 
           class="bg-white rounded-2xl p-6 border border-stone-200/80 shadow-xs hover:border-amber-400 hover:shadow-md transition-all group flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-stone-400 uppercase tracking-wider mb-1">{{ __('Gestión') }}</p>
                <h4 class="text-base font-bold text-slate-900 group-hover:text-amber-600 transition-colors">{{ __('Todas las Peticiones') }}</h4>
                <p class="text-xs text-stone-500 mt-0.5">{{ __('Filtra, asigna y da seguimiento') }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </div>
        </a>

        <a href="{{ route('admin.intercessors.index') }}" wire:navigate 
           class="bg-white rounded-2xl p-6 border border-stone-200/80 shadow-xs hover:border-amber-400 hover:shadow-md transition-all group flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-stone-400 uppercase tracking-wider mb-1">{{ __('Equipo') }}</p>
                <h4 class="text-base font-bold text-slate-900 group-hover:text-amber-600 transition-colors">{{ __('Equipo de Intercesores') }}</h4>
                <p class="text-xs text-stone-500 mt-0.5">{{ __('Cargas de trabajo y oración activa') }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </div>
        </a>

        <a href="{{ route('admin.users.index') }}" wire:navigate 
           class="bg-white rounded-2xl p-6 border border-stone-200/80 shadow-xs hover:border-amber-400 hover:shadow-md transition-all group flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-stone-400 uppercase tracking-wider mb-1">{{ __('Seguridad') }}</p>
                <h4 class="text-base font-bold text-slate-900 group-hover:text-amber-600 transition-colors">{{ __('Cuentas y Permisos') }}</h4>
                <p class="text-xs text-stone-500 mt-0.5">{{ __('Administra accesos y roles') }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </div>
        </a>

    </div>

</div>
