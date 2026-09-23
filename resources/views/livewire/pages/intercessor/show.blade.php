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

<div class="space-y-6">
    
    <!-- Top Link -->
    <div>
        <a href="{{ route('intercessor.dashboard') }}" wire:navigate 
           class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-stone-500 hover:text-slate-950 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>{{ __('Volver a mis peticiones') }}</span>
        </a>
    </div>

    <!-- Request Details Card -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-stone-200/80 shadow-xs space-y-6">
        
        <div class="flex items-start justify-between gap-4 border-b border-stone-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-amber-500/10 text-amber-700 font-bold text-sm flex items-center justify-center border border-amber-500/20">
                    {{ substr($prayerRequest->requester_name ?: 'A', 0, 1) }}
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900 leading-tight">
                        {{ $prayerRequest->requester_name ?: __('Petición Anónima') }}
                    </h2>
                    <p class="text-xs text-stone-400 mt-0.5">
                        {{ $prayerRequest->country_name ?? __('Zona desconocida') }} · {{ $prayerRequest->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>

            <span class="text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider {{ $prayerRequest->status->badgeColor() }}">
                {{ $prayerRequest->status->label() }}
            </span>
        </div>

        <!-- Content -->
        <div class="p-5 rounded-xl bg-stone-50 border border-stone-200/80">
            <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block mb-2">{{ __('Motivo de Oración') }}</span>
            <p class="text-slate-800 text-sm sm:text-base leading-relaxed italic font-serif whitespace-pre-wrap">
                “{{ $prayerRequest->translated_content }}”
            </p>
        </div>

        <!-- Action Button for Intercessor -->
        @if ($prayerRequest->status === PrayerRequestStatus::Assigned)
            <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold text-slate-900">{{ __('¿Has comenzado a orar por esta petición?') }}</p>
                    <p class="text-[11px] text-stone-500">{{ __('Haz clic para actualizar el estado a "En Oración"') }}</p>
                </div>
                <button type="button" 
                        wire:click="markPraying" 
                        class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider transition-all shadow-xs shrink-0">
                    {{ __('Estoy orando por esta petición') }}
                </button>
            </div>
        @endif

        @if ($prayerRequest->is_answered)
            <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4">
                <p class="text-xs font-bold text-emerald-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ __('Esta petición fue marcada como contestada. ¡Damos gracias a Dios!') }}</span>
                </p>
            </div>
        @endif

    </div>

    <!-- Live Chat with Requester -->
    @if (! $prayerRequest->is_public)
        <livewire:prayer-chat
            :prayer-request="$prayerRequest"
            :viewer-role="\App\Enums\MessageAuthorType::Intercessor"
            :viewer-user-id="auth()->id()"
            :key="'chat-'.$prayerRequest->id"
        />
    @endif

</div>
