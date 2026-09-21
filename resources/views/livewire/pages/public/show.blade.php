<?php

use App\Enums\MessageAuthorType;
use App\Enums\PrayerRequestStatus;
use App\Models\PrayerRequest;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public PrayerRequest $prayerRequest;

    public string $answer_note = '';

    public bool $showAnsweredForm = false;

    public function mount(PrayerRequest $prayerRequest): void
    {
        $this->prayerRequest = $prayerRequest;
    }

    public function markAnswered(): void
    {
        $this->prayerRequest->markAnswered($this->answer_note ?: null);
        $this->showAnsweredForm = false;
        $this->dispatch('prayer-request-updated');
    }

    public function getViewerRoleProperty(): MessageAuthorType
    {
        $user = auth()->user();

        if (! $user) {
            return MessageAuthorType::Requester;
        }

        if ($user->isAdmin()) {
            return MessageAuthorType::Admin;
        }

        if ($user->isIntercessor() && $this->prayerRequest->isAssignedTo($user)) {
            return MessageAuthorType::Intercessor;
        }

        return MessageAuthorType::Requester;
    }
}; ?>

<div class="space-y-6">
    <div class="rounded-md bg-amber-50 border border-amber-200 px-4 py-3">
        <p class="text-sm text-amber-800">
            <strong>Guardá este enlace.</strong> Es la única forma de volver a ver tu petición, su estado y la conversación.
        </p>
    </div>

    <div>
        <div class="flex items-center justify-between gap-2 flex-wrap">
            <h1 class="text-xl font-semibold text-gray-800">
                {{ $prayerRequest->requester_name ?: 'Petición anónima' }}
            </h1>
            <span class="text-xs font-medium px-2 py-1 rounded-full {{ $prayerRequest->status->badgeColor() }}">
                {{ $prayerRequest->status->label() }}
            </span>
        </div>
        <p class="text-xs text-gray-400 mt-1">Enviada {{ $prayerRequest->created_at->diffForHumans() }}</p>
        <p class="mt-3 text-gray-700 whitespace-pre-wrap">{{ $prayerRequest->content }}</p>
    </div>

    @if ($prayerRequest->is_answered)
        <div class="rounded-md bg-green-50 border border-green-200 px-4 py-3">
            <p class="text-sm font-medium text-green-800">Esta petición fue marcada como contestada 🙏</p>
            @if ($prayerRequest->answer_note)
                <p class="text-sm text-green-700 mt-1 whitespace-pre-wrap">{{ $prayerRequest->answer_note }}</p>
            @endif
        </div>
    @else
        <div>
            @if (! $showAnsweredForm)
                <button type="button" wire:click="$set('showAnsweredForm', true)" class="text-sm text-indigo-600 underline">
                    Marcar como contestada
                </button>
            @else
                <div class="border border-gray-200 rounded-md p-3 space-y-2">
                    <x-input-label for="answer_note" value="¿Cómo respondió Dios? (opcional)" />
                    <textarea wire:model="answer_note" id="answer_note" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    <div class="flex gap-2">
                        <x-primary-button wire:click="markAnswered">Confirmar</x-primary-button>
                        <x-secondary-button wire:click="$set('showAnsweredForm', false)">Cancelar</x-secondary-button>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <livewire:prayer-chat
        :prayer-request="$prayerRequest"
        :viewer-role="$this->viewerRole"
        :viewer-user-id="auth()->id()"
        :key="'chat-'.$prayerRequest->id"
    />
</div>
