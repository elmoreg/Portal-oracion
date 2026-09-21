<?php

use App\Enums\PrayerRequestStatus;
use App\Models\PrayerRequest;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public PrayerRequest $prayerRequest;

    public function mount(PrayerRequest $prayerRequest): void
    {
        abort_unless($prayerRequest->isAssignedTo(auth()->user()), 403);

        $this->prayerRequest = $prayerRequest;
    }

    public function markPraying(): void
    {
        if ($this->prayerRequest->status === PrayerRequestStatus::Assigned) {
            $this->prayerRequest->update(['status' => PrayerRequestStatus::Praying]);
        }
    }
}; ?>

<div>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <a href="{{ route('intercessor.dashboard') }}" wire:navigate class="text-sm text-indigo-600">&larr; Volver a mis peticiones</a>

            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex items-center justify-between gap-2 flex-wrap">
                    <h1 class="text-xl font-semibold text-gray-800">
                        {{ $prayerRequest->requester_name ?: 'Petición anónima' }}
                    </h1>
                    <span class="text-xs font-medium px-2 py-1 rounded-full {{ $prayerRequest->status->badgeColor() }}">
                        {{ $prayerRequest->status->label() }}
                    </span>
                </div>
                <p class="text-xs text-gray-400 mt-1">
                    {{ $prayerRequest->country_name ?? 'Zona desconocida' }} · enviada {{ $prayerRequest->created_at->diffForHumans() }}
                </p>
                <p class="mt-4 text-gray-700 whitespace-pre-wrap">{{ $prayerRequest->content }}</p>

                @if ($prayerRequest->status === PrayerRequestStatus::Assigned)
                    <div class="mt-4">
                        <x-primary-button wire:click="markPraying">Estoy orando por esta petición</x-primary-button>
                    </div>
                @endif

                @if ($prayerRequest->is_answered)
                    <div class="mt-4 rounded-md bg-green-50 border border-green-200 px-4 py-3">
                        <p class="text-sm font-medium text-green-800">Esta petición fue marcada como contestada 🙏</p>
                    </div>
                @endif
            </div>

            <livewire:prayer-chat
                :prayer-request="$prayerRequest"
                :viewer-role="\App\Enums\MessageAuthorType::Intercessor"
                :viewer-user-id="auth()->id()"
                :key="'chat-'.$prayerRequest->id"
            />
        </div>
    </div>
</div>
