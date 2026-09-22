<?php

use App\Enums\PrayerRequestStatus;
use App\Enums\UserRole;
use App\Models\PrayerRequest;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public PrayerRequest $prayerRequest;

    /** @var array<int, int> */
    public array $selectedIntercessors = [];

    public string $status = '';

    public function mount(PrayerRequest $prayerRequest): void
    {
        $this->prayerRequest = $prayerRequest;
        $this->selectedIntercessors = $prayerRequest->intercessors()->pluck('users.id')->all();
        $this->status = $prayerRequest->status->value;
    }

    public function getIntercessorsProperty()
    {
        return User::where('role', UserRole::Intercessor)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function saveAssignments(): void
    {
        $syncData = collect($this->selectedIntercessors)
            ->mapWithKeys(fn ($id) => [$id => [
                'assigned_by' => auth()->id(),
                'assigned_at' => now(),
            ]])
            ->all();

        $this->prayerRequest->intercessors()->sync($syncData);

        if ($this->selectedIntercessors !== [] && $this->prayerRequest->status === PrayerRequestStatus::Pending) {
            $this->prayerRequest->update(['status' => PrayerRequestStatus::Assigned]);
            $this->status = PrayerRequestStatus::Assigned->value;
        }

        $this->dispatch('assignments-saved');
    }

    public function updateStatus(): void
    {
        $this->prayerRequest->update(['status' => PrayerRequestStatus::from($this->status)]);
    }
}; ?>

<div>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <a href="{{ route('admin.prayer-requests.index') }}" wire:navigate class="text-sm text-indigo-600">&larr; Volver al listado</a>

            <div class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                <div class="flex items-center justify-between gap-2 flex-wrap">
                    <h1 class="text-xl font-semibold text-gray-800">
                        {{ $prayerRequest->requester_name ?: 'Petición anónima' }}
                    </h1>
                    <select wire:model="status" wire:change="updateStatus" class="rounded-md border-gray-300 text-sm">
                        @foreach (PrayerRequestStatus::cases() as $case)
                            <option value="{{ $case->value }}">{{ $case->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <p class="text-xs text-gray-400">
                    Zona: {{ $prayerRequest->country_name ?? 'Desconocida' }}
                    ({{ $prayerRequest->ip_address ?? 'IP no registrada' }})
                    · enviada {{ $prayerRequest->created_at->diffForHumans() }}
                </p>

                <p class="text-gray-700 whitespace-pre-wrap">{{ $prayerRequest->translated_content }}</p>

                @if ($prayerRequest->is_answered)
                    <div class="rounded-md bg-green-50 border border-green-200 px-4 py-3">
                        <p class="text-sm font-medium text-green-800">Contestada 🙏</p>
                        @if ($prayerRequest->answer_note)
                            <p class="text-sm text-green-700 mt-1">{{ $prayerRequest->translated_answer_note }}</p>
                        @endif
                    </div>
                @endif

                <p class="text-[11px] text-gray-400">{!! config('geoip.attribution_html') !!}</p>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="font-medium text-gray-800 mb-3">Asignar intercesores</h2>
                <div class="grid sm:grid-cols-2 gap-2 max-h-64 overflow-y-auto">
                    @forelse ($this->intercessors as $intercessor)
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" wire:model="selectedIntercessors" value="{{ $intercessor->id }}" class="rounded border-gray-300">
                            {{ $intercessor->name }}
                        </label>
                    @empty
                        <p class="text-sm text-gray-400">Todavía no hay personas inscritas para orar.</p>
                    @endforelse
                </div>
                <div class="mt-4">
                    <x-primary-button wire:click="saveAssignments">Guardar asignación</x-primary-button>
                    <span wire:loading.remove wire:target="saveAssignments" x-data="{ shown: false }" x-init="Livewire.on('assignments-saved', () => { shown = true; setTimeout(() => shown = false, 2000) })" x-show="shown" class="text-sm text-green-600 ml-2">Guardado</span>
                </div>
            </div>

            @if ($prayerRequest->is_public)
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h2 class="font-medium text-gray-800 mb-4">Comentarios Comunitarios</h2>
                    <div class="space-y-4">
                        @forelse ($prayerRequest->publicComments as $comment)
                            <div class="p-3 rounded-lg border border-gray-100 bg-gray-50">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="font-medium text-sm text-gray-900">{{ $comment->author_name ?: 'Anónimo' }}</span>
                                    <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-gray-700 text-sm whitespace-pre-wrap">{{ $comment->translated_body }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Nadie ha dejado un comentario público aún.</p>
                        @endforelse
                    </div>
                </div>
            @else
                <livewire:prayer-chat
                    :prayer-request="$prayerRequest"
                    :viewer-role="\App\Enums\MessageAuthorType::Admin"
                    :viewer-user-id="auth()->id()"
                    :key="'chat-'.$prayerRequest->id"
                />
            @endif
        </div>
    </div>
</div>
