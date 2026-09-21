<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public function getRequestsProperty()
    {
        return auth()->user()->assignedPrayerRequests()->get();
    }
}; ?>

<div>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-2">Peticiones que te asignaron</h2>
            @forelse ($this->requests as $prayerRequest)
                <a href="{{ route('intercessor.prayer-requests.show', $prayerRequest) }}" wire:navigate
                   class="block bg-white shadow-sm rounded-lg p-4 border border-gray-100 hover:border-indigo-300 transition">
                    <div class="flex items-center justify-between gap-2 flex-wrap">
                        <span class="text-sm font-medium text-gray-800">
                            {{ $prayerRequest->requester_name ?: 'Petición anónima' }}
                        </span>
                        <span class="text-xs font-medium px-2 py-1 rounded-full {{ $prayerRequest->status->badgeColor() }}">
                            {{ $prayerRequest->status->label() }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ $prayerRequest->content }}</p>
                    <p class="text-xs text-gray-400 mt-2">
                        {{ $prayerRequest->country_name ?? 'Zona desconocida' }} · asignada {{ $prayerRequest->pivot->assigned_at?->diffForHumans() }}
                    </p>
                </a>
            @empty
                <div class="bg-white shadow-sm rounded-lg p-6 text-center text-gray-500 text-sm">
                    Todavía no tenés peticiones asignadas. El equipo de administración te va a asignar
                    peticiones para orar por ellas.
                </div>
            @endforelse
        </div>
    </div>
</div>
