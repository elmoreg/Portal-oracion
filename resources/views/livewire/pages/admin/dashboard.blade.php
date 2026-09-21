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
    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Panel de administración</h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Total de peticiones</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $this->stats['total'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Sin asignar</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $this->stats['pending'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">En oración</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $this->stats['praying'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500">Contestadas</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $this->stats['answered'] }}</p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-medium text-gray-800 mb-3">Peticiones por zona</h3>
                    <ul class="space-y-2">
                        @forelse ($this->zones as $zone)
                            <li class="flex justify-between text-sm text-gray-600">
                                <span>{{ $zone->country_name }}</span>
                                <span class="font-medium">{{ $zone->total }}</span>
                            </li>
                        @empty
                            <li class="text-sm text-gray-400">Todavía no hay datos de zona geográfica.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-medium text-gray-800">Últimas sin asignar</h3>
                        <a href="{{ route('admin.prayer-requests.index') }}" wire:navigate class="text-sm text-indigo-600">Ver todas</a>
                    </div>
                    <ul class="space-y-3">
                        @forelse ($this->recent as $prayerRequest)
                            <li>
                                <a href="{{ route('admin.prayer-requests.show', $prayerRequest) }}" wire:navigate class="text-sm text-gray-700 hover:text-indigo-600 line-clamp-1 block">
                                    {{ $prayerRequest->content }}
                                </a>
                            </li>
                        @empty
                            <li class="text-sm text-gray-400">No hay peticiones pendientes de asignar 🎉</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
