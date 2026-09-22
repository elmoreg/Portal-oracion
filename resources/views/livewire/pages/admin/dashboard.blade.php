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

<div>
    <x-slot name="header">
        Panel de administración
    </x-slot>

    <div class="space-y-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="glass-panel rounded-2xl p-6 relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-soul-indigo/5 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out"></div>
                <p class="text-sm font-medium text-soul-accent mb-1 relative z-10">Total de peticiones</p>
                <p class="text-4xl font-serif text-soul-indigo relative z-10">{{ $this->stats['total'] }}</p>
            </div>
            
            <div class="glass-panel rounded-2xl p-6 relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/5 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out"></div>
                <div class="flex justify-between items-start mb-1 relative z-10">
                    <p class="text-sm font-medium text-soul-accent">Sin asignar</p>
                    <span class="flex h-3 w-3 relative">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                    </span>
                </div>
                <p class="text-4xl font-serif text-soul-indigo relative z-10">{{ $this->stats['pending'] }}</p>
            </div>
            
            <div class="glass-panel rounded-2xl p-6 relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/5 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out"></div>
                <p class="text-sm font-medium text-soul-accent mb-1 relative z-10">En oración</p>
                <p class="text-4xl font-serif text-soul-indigo relative z-10">{{ $this->stats['praying'] }}</p>
            </div>
            
            <div class="glass-panel rounded-2xl p-6 relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-green-500/5 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out"></div>
                <p class="text-sm font-medium text-soul-accent mb-1 relative z-10">Contestadas</p>
                <p class="text-4xl font-serif text-soul-indigo relative z-10">{{ $this->stats['answered'] }}</p>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <div class="glass-panel rounded-2xl p-8">
                <h3 class="font-serif text-xl text-soul-indigo mb-6">Peticiones por zona</h3>
                <ul class="space-y-4">
                    @forelse ($this->zones as $zone)
                        @php
                            $percentage = $this->stats['total'] > 0 ? ($zone->total / $this->stats['total']) * 100 : 0;
                        @endphp
                        <li>
                            <div class="flex justify-between text-sm text-soul-indigo font-medium mb-1.5">
                                <span>{{ $zone->country_name }}</span>
                                <span>{{ $zone->total }}</span>
                            </div>
                            <div class="w-full bg-soul-indigo/10 rounded-full h-2">
                                <div class="bg-soul-gold h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </li>
                    @empty
                        <li class="text-sm text-soul-accent italic text-center py-4">Todavía no hay datos de zona geográfica.</li>
                    @endforelse
                </ul>
            </div>

            <div class="glass-panel rounded-2xl p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-serif text-xl text-soul-indigo">Últimas sin asignar</h3>
                    <a href="{{ route('admin.prayer-requests.index') }}" wire:navigate class="text-sm font-medium text-soul-gold hover:text-soul-indigo transition-colors flex items-center">
                        Ver todas
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
                <ul class="space-y-4">
                    @forelse ($this->recent as $prayerRequest)
                        <li>
                            <a href="{{ route('admin.prayer-requests.show', $prayerRequest) }}" wire:navigate class="group block p-4 rounded-xl border border-soul-indigo/5 bg-white/50 hover:bg-white hover:border-soul-gold/30 hover:shadow-sm transition-all">
                                <p class="text-sm text-soul-indigo font-medium line-clamp-1 mb-1 group-hover:text-soul-gold transition-colors">
                                    {{ $prayerRequest->translated_content }}
                                </p>
                                <div class="flex items-center justify-between text-xs text-soul-accent">
                                    <span>{{ $prayerRequest->created_at->diffForHumans() }}</span>
                                    <span>{{ $prayerRequest->country_name ?? 'Zona desconocida' }}</span>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="text-center py-8">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-50 text-green-500 mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <p class="text-sm font-medium text-soul-indigo">Todo al día</p>
                            <p class="text-xs text-soul-accent mt-1">No hay peticiones pendientes de asignar</p>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- Accesos rápidos --}}
        <div class="grid sm:grid-cols-3 gap-4">
            <a href="{{ route('admin.prayer-requests.index') }}" wire:navigate class="glass-panel rounded-2xl p-6 group hover:border-soul-gold/30 hover:shadow-sm transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-soul-accent mb-1">Peticiones</p>
                        <p class="font-serif text-lg text-soul-indigo group-hover:text-soul-gold transition-colors">Gestionar peticiones</p>
                    </div>
                    <svg class="w-5 h-5 text-soul-accent group-hover:text-soul-gold transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </a>

            <a href="{{ route('admin.intercessors.index') }}" wire:navigate class="glass-panel rounded-2xl p-6 group hover:border-soul-gold/30 hover:shadow-sm transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-soul-accent mb-1">Intercesores</p>
                        <p class="font-serif text-lg text-soul-indigo group-hover:text-soul-gold transition-colors">Equipo de oración</p>
                    </div>
                    <svg class="w-5 h-5 text-soul-accent group-hover:text-soul-gold transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </a>

            <a href="{{ route('admin.users.index') }}" wire:navigate class="glass-panel rounded-2xl p-6 group hover:border-soul-gold/30 hover:shadow-sm transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-soul-accent mb-1">Usuarios</p>
                        <p class="font-serif text-lg text-soul-indigo group-hover:text-soul-gold transition-colors">Gestionar cuentas</p>
                    </div>
                    <svg class="w-5 h-5 text-soul-accent group-hover:text-soul-gold transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </a>
        </div>
    </div>
</div>
