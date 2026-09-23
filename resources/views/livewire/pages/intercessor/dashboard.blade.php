<?php

use App\Enums\PrayerRequestStatus;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public function getRequestsProperty()
    {
        return auth()->user()->assignedPrayerRequests()->latest()->get();
    }

    public function getStatsProperty(): array
    {
        $reqs = $this->requests;

        return [
            'total' => $reqs->count(),
            'assigned' => $reqs->where('status', PrayerRequestStatus::Assigned)->count(),
            'praying' => $reqs->where('status', PrayerRequestStatus::Praying)->count(),
            'answered' => $reqs->where('status', PrayerRequestStatus::Answered)->count(),
        ];
    }
}; ?>

<div class="space-y-8">
    <x-slot name="header">
        {{ __('Panel de Intercesión') }}
    </x-slot>

    <!-- Intercessor Stat Cards Grid (TailAdmin Style) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="bg-white rounded-2xl p-6 border border-stone-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <span class="text-xs font-bold text-stone-500 bg-stone-100 px-2.5 py-1 rounded-full">
                    {{ __('Asignadas') }}
                </span>
            </div>
            <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">{{ __('Total Peticiones') }}</p>
            <h3 class="text-3xl font-extrabold text-slate-900">{{ $this->stats['total'] }}</h3>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-stone-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full">
                    {{ __('Por Iniciar') }}
                </span>
            </div>
            <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">{{ __('Nuevas Asignadas') }}</p>
            <h3 class="text-3xl font-extrabold text-slate-900">{{ $this->stats['assigned'] }}</h3>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-stone-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </div>
                <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-full">
                    {{ __('Activas') }}
                </span>
            </div>
            <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">{{ __('En Oración') }}</p>
            <h3 class="text-3xl font-extrabold text-slate-900">{{ $this->stats['praying'] }}</h3>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-stone-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full">
                    {{ __('Respondidas') }}
                </span>
            </div>
            <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">{{ __('Contestadas') }}</p>
            <h3 class="text-3xl font-extrabold text-slate-900">{{ $this->stats['answered'] }}</h3>
        </div>

    </div>

    <!-- Main Content Container -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs p-6 sm:p-8 space-y-6">
        <div class="flex items-center justify-between border-b border-stone-100 pb-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900">{{ __('Peticiones Asignadas a tu Cuidado') }}</h3>
                <p class="text-xs text-stone-500">{{ __('Acompaña y ora por cada una de estas necesidades') }}</p>
            </div>
            <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-700 font-bold text-xs">
                {{ count($this->requests) }} {{ __('peticiones') }}
            </span>
        </div>

        <!-- Grid of Assigned Requests -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse ($this->requests as $prayerRequest)
                <div class="rounded-2xl border border-stone-200/80 bg-stone-50/50 hover:bg-white hover:border-amber-400 hover:shadow-md transition-all p-6 flex flex-col justify-between group">
                    <div>
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-amber-500/10 text-amber-700 font-bold text-xs flex items-center justify-center border border-amber-500/20">
                                    {{ substr($prayerRequest->requester_name ?: 'A', 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900">
                                        {{ $prayerRequest->requester_name ?: __('Petición Anónima') }}
                                    </h4>
                                    <span class="text-[11px] text-stone-400">
                                        {{ $prayerRequest->country_name ?? __('Zona desconocida') }}
                                    </span>
                                </div>
                            </div>

                            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider {{ $prayerRequest->status->badgeColor() }}">
                                {{ $prayerRequest->status->label() }}
                            </span>
                        </div>

                        <p class="text-xs text-stone-700 italic font-serif leading-relaxed line-clamp-3 mb-4">
                            “{{ $prayerRequest->translated_content }}”
                        </p>
                    </div>

                    <div class="pt-4 border-t border-stone-200/60 flex items-center justify-between text-xs mt-auto">
                        <span class="text-stone-400 text-[11px]">
                            Asignada {{ $prayerRequest->pivot->assigned_at?->diffForHumans() ?? 'recientemente' }}
                        </span>

                        <a href="{{ route('intercessor.prayer-requests.show', $prayerRequest) }}" wire:navigate 
                           class="px-4 py-2 rounded-xl bg-slate-950 hover:bg-amber-500 text-white hover:text-slate-950 font-bold text-xs uppercase tracking-wider transition-all inline-flex items-center gap-1.5 shadow-xs">
                            <span>{{ __('Acompañar / Orar') }}</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-stone-400">
                    <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </div>
                    <h4 class="text-sm font-bold text-slate-800 mb-1">{{ __('No tienes peticiones asignadas en este momento') }}</h4>
                    <p class="text-xs text-stone-500 max-w-sm mx-auto">{{ __('El equipo de administración te asignará peticiones para que puedas interceder por ellas.') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
