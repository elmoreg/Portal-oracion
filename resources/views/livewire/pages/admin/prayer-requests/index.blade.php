<?php

use App\Enums\PrayerRequestStatus;
use App\Models\PrayerRequest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] class extends Component
{
    use WithPagination;

    #[Url]
    public string $status = '';

    #[Url]
    public string $country = '';

    #[Url]
    public string $search = '';

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedCountry(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function getCountriesProperty()
    {
        return PrayerRequest::query()
            ->whereNotNull('country_name')
            ->distinct()
            ->orderBy('country_name')
            ->pluck('country_name');
    }

    public function getRequestsProperty()
    {
        return PrayerRequest::query()
            ->withCount('intercessors')
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->country, fn ($q) => $q->where('country_name', $this->country))
            ->when($this->search, fn ($q) => $q->where(function ($sub) {
                $sub->where('content', 'like', '%'.$this->search.'%')
                    ->orWhere('requester_name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            }))
            ->latest()
            ->paginate(12);
    }
}; ?>

<div class="space-y-6">
    <x-slot name="header">
        {{ __('Gestión de Peticiones de Oración') }}
    </x-slot>

    <!-- Main Table Card Container (TailAdmin Style) -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
        
        <!-- Filter & Search Toolbar Header -->
        <div class="p-6 border-b border-stone-200/80 bg-stone-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            
            <!-- Left: Search Box -->
            <div class="relative w-full sm:w-80">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-stone-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" 
                       type="text" 
                       placeholder="{{ __('Buscar por motivo o nombre...') }}" 
                       class="w-full bg-white text-xs rounded-xl border border-stone-200 pl-9 pr-3 py-2.5 focus:border-amber-500 focus:ring-amber-500 placeholder-stone-400 transition-colors">
            </div>

            <!-- Right: Filter Dropdowns -->
            <div class="flex items-center gap-3 flex-wrap">
                <!-- Status Filter -->
                <select wire:model.live="status" class="bg-white text-xs font-semibold text-slate-700 rounded-xl border border-stone-200 px-3.5 py-2.5 focus:border-amber-500 focus:ring-amber-500">
                    <option value="">{{ __('Todos los estados') }}</option>
                    @foreach (PrayerRequestStatus::cases() as $case)
                        <option value="{{ $case->value }}">{{ $case->label() }}</option>
                    @endforeach
                </select>

                <!-- Country Filter -->
                <select wire:model.live="country" class="bg-white text-xs font-semibold text-slate-700 rounded-xl border border-stone-200 px-3.5 py-2.5 focus:border-amber-500 focus:ring-amber-500">
                    <option value="">{{ __('Todas las zonas') }}</option>
                    @foreach ($this->countries as $countryName)
                        <option value="{{ $countryName }}">{{ $countryName }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-stone-50/80 border-b border-stone-200/80 text-[11px] font-bold uppercase tracking-wider text-stone-500">
                        <th class="py-4 px-6">{{ __('Solicitante / Petición') }}</th>
                        <th class="py-4 px-4">{{ __('Zona / País') }}</th>
                        <th class="py-4 px-4">{{ __('Estado') }}</th>
                        <th class="py-4 px-4 text-center">{{ __('Intercesores') }}</th>
                        <th class="py-4 px-4">{{ __('Fecha') }}</th>
                        <th class="py-4 px-6 text-right">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-xs">
                    @forelse ($this->requests as $prayerRequest)
                        <tr class="hover:bg-stone-50/60 transition-colors group">
                            
                            <!-- Solicitante y Petición -->
                            <td class="py-4 px-6 max-w-sm">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-full bg-amber-500/10 text-amber-700 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5 border border-amber-500/20">
                                        {{ substr($prayerRequest->requester_name ?: 'A', 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 mb-0.5">
                                            <span class="font-bold text-slate-900 truncate">
                                                {{ $prayerRequest->requester_name ?: __('Petición Anónima') }}
                                            </span>
                                            @if($prayerRequest->is_public)
                                                <span class="px-2 py-0.2 rounded-full bg-amber-50 text-amber-700 text-[10px] font-bold uppercase">
                                                    {{ __('Muro') }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-stone-600 line-clamp-2 italic font-serif leading-relaxed">
                                            “{{ $prayerRequest->translated_content }}”
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Zona -->
                            <td class="py-4 px-4 text-stone-600 font-medium whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-lg bg-stone-100 text-stone-700 text-[11px] font-semibold">
                                    {{ $prayerRequest->country_name ?? '—' }}
                                </span>
                            </td>

                            <!-- Estado -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                <span class="text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider {{ $prayerRequest->status->badgeColor() }}">
                                    {{ $prayerRequest->status->label() }}
                                </span>
                            </td>

                            <!-- Intercesores Asignados -->
                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-bold text-xs {{ $prayerRequest->intercessors_count > 0 ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    {{ $prayerRequest->intercessors_count }}
                                </span>
                            </td>

                            <!-- Fecha -->
                            <td class="py-4 px-4 text-stone-400 whitespace-nowrap text-[11px] font-medium">
                                {{ $prayerRequest->created_at->diffForHumans() }}
                            </td>

                            <!-- Acciones -->
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <a href="{{ route('admin.prayer-requests.show', $prayerRequest) }}" wire:navigate 
                                   class="px-4 py-2 rounded-xl bg-slate-950 hover:bg-amber-500 text-white hover:text-slate-950 font-bold text-xs uppercase tracking-wider transition-all inline-flex items-center gap-1.5 shadow-xs">
                                    <span>{{ __('Gestionar') }}</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-stone-400">
                                <svg class="w-10 h-10 mx-auto mb-2 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                <p class="font-bold text-slate-700">{{ __('No se encontraron peticiones') }}</p>
                                <p class="text-xs text-stone-400 mt-0.5">{{ __('Intenta cambiando los filtros o el término de búsqueda.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="p-4 border-t border-stone-100 bg-stone-50/50">
            {{ $this->requests->links() }}
        </div>

    </div>
</div>
