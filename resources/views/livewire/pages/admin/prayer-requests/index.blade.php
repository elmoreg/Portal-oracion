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

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedCountry(): void
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
            ->latest()
            ->paginate(15);
    }
}; ?>

<div>
    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Peticiones de oración</h2>

            <div class="flex gap-3 flex-wrap">
                <select wire:model.live="status" class="rounded-md border-gray-300 text-sm">
                    <option value="">Todos los estados</option>
                    @foreach (PrayerRequestStatus::cases() as $case)
                        <option value="{{ $case->value }}">{{ $case->label() }}</option>
                    @endforeach
                </select>

                <select wire:model.live="country" class="rounded-md border-gray-300 text-sm">
                    <option value="">Todas las zonas</option>
                    @foreach ($this->countries as $countryName)
                        <option value="{{ $countryName }}">{{ $countryName }}</option>
                    @endforeach
                </select>
            </div>

            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs text-gray-500 uppercase">
                        <tr>
                            <th class="px-4 py-2">Petición</th>
                            <th class="px-4 py-2">Zona</th>
                            <th class="px-4 py-2">Estado</th>
                            <th class="px-4 py-2">Intercesores</th>
                            <th class="px-4 py-2">Fecha</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($this->requests as $prayerRequest)
                            <tr>
                                <td class="px-4 py-3 max-w-xs">
                                    <p class="text-gray-700 line-clamp-1">{{ $prayerRequest->translated_content }}</p>
                                </td>
                                <td class="px-4 py-3 text-gray-500">{{ $prayerRequest->country_name ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="text-xs font-medium px-2 py-1 rounded-full {{ $prayerRequest->status->badgeColor() }}">
                                        {{ $prayerRequest->status->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-500">{{ $prayerRequest->intercessors_count }}</td>
                                <td class="px-4 py-3 text-gray-400">{{ $prayerRequest->created_at->diffForHumans() }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.prayer-requests.show', $prayerRequest) }}" wire:navigate class="text-indigo-600">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-400">No hay peticiones con esos filtros.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $this->requests->links() }}
        </div>
    </div>
</div>
