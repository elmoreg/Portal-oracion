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

    public string $emailToSend = '';

    public bool $emailSent = false;

    public function mount(PrayerRequest $prayerRequest): void
    {
        $this->prayerRequest = $prayerRequest;
    }

    public function sendLinkToEmail(): void
    {
        $this->validate([
            'emailToSend' => ['required', 'email', 'max:255'],
        ]);

        \Illuminate\Support\Facades\Mail::to($this->emailToSend)->send(new \App\Mail\PrayerRequestCreated($this->prayerRequest));
        
        $this->emailSent = true;
        $this->emailToSend = '';
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
    @if (session('petition_created'))
        <!-- Encouragement & Success Card -->
        <div class="rounded-2xl bg-gradient-to-br from-amber-50 via-white to-amber-50/50 border border-amber-300 p-6 sm:p-7 shadow-md text-start space-y-4">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/15 text-amber-600 flex items-center justify-center shrink-0 shadow-inner">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-900 border border-amber-200">
                            {{ __('Petición Registrada con Éxito') }}
                        </span>
                    </div>
                    <h2 class="font-cinzel text-xl sm:text-2xl font-bold text-slate-900 leading-tight">
                        {{ __('¡Tu petición ha sido recibida con amor y fe!') }}
                    </h2>
                </div>
            </div>

            <p class="text-sm text-slate-700 leading-relaxed font-light">
                {{ __('No estás solo ni sola en este momento. Tu necesidad ha sido puesta en manos de Dios y nuestro equipo de intercesores ya ha sido notificado para orar por ti con dedicación, empatía y absoluta confidencialidad.') }}
            </p>

            <!-- Scripture Quote -->
            <div class="rounded-xl bg-amber-500/10 border-l-4 border-amber-500 p-4">
                <p class="text-xs sm:text-sm font-serif italic text-slate-800 leading-relaxed">
                    {{ __('«No se angustien por nada, y en cualquier circunstancia, acudan a Dios orando y suplicando, y dándole gracias. Y la paz de Dios, que sobrepasa todo entendimiento, cuidará sus corazones y sus pensamientos en Cristo Jesús.»') }}
                </p>
                <p class="text-[11px] font-bold tracking-widest uppercase text-amber-700 mt-1.5">
                    {{ __('— FILIPENSES 4:6-7') }}
                </p>
            </div>

            <!-- Steps / What happens next -->
            <div class="pt-3 border-t border-stone-200 grid sm:grid-cols-3 gap-3 text-xs text-slate-600">
                <div class="flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-700 font-bold flex items-center justify-center shrink-0 text-[11px]">1</span>
                    <span>{{ __('Un intercesor levantará una oración dedicada por ti.') }}</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-700 font-bold flex items-center justify-center shrink-0 text-[11px]">2</span>
                    <span>{{ __('Recibirás mensajes y oraciones en el chat privado aquí abajo.') }}</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-700 font-bold flex items-center justify-center shrink-0 text-[11px]">3</span>
                    <span>{{ __('Puedes volver cuando quieras utilizando este mismo enlace.') }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Notice Banner -->
    <div class="rounded-xl bg-amber-500/10 border border-amber-500/30 p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <p class="text-xs sm:text-sm text-slate-800 leading-relaxed">
            <strong class="font-bold text-amber-800">{{ __('Guarda este enlace privado:') }}</strong> {{ __('Es el único medio para volver a consultar tu petición, ver el estado de oración y responder a los intercesores.') }}
        </p>
    </div>

    @if (empty($prayerRequest->email) && $this->viewerRole === MessageAuthorType::Requester)
        @if ($emailSent)
            <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 mb-6">
                <p class="text-sm font-bold text-emerald-800 flex items-center gap-1.5">
                    <span>✅</span> {{ __('¡Enlace enviado correctamente a tu correo!') }}
                </p>
            </div>
        @else
            <div class="rounded-xl bg-white border border-indigo-100 p-4 shadow-sm mb-6">
                <h3 class="text-sm font-bold text-indigo-900 mb-2">{{ __('¿Quieres recibir este enlace por correo?') }}</h3>
                <p class="text-xs text-slate-600 mb-3">{{ __('No guardaremos tu correo en nuestra base de datos, solo lo usaremos en este momento para enviarte el enlace de seguimiento.') }}</p>
                <div class="flex gap-2">
                    <input type="email" wire:model="emailToSend" placeholder="{{ __('Ingresa tu correo') }}" class="block w-full rounded border-stone-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-2">
                    <button type="button" wire:click="sendLinkToEmail" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-sm whitespace-nowrap">
                        {{ __('Enviar enlace') }}
                    </button>
                </div>
                <x-input-error :messages="$errors->get('emailToSend')" class="mt-2" />
            </div>
        @endif
    @endif

    <!-- Request Details Card -->
    <div class="bg-stone-50 rounded-xl p-5 sm:p-6 border border-stone-200">
        <div class="flex items-center justify-between gap-3 flex-wrap border-b border-stone-200/80 pb-3 mb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-amber-500/20 text-amber-600 flex items-center justify-center font-bold text-xs">
                    {{ substr($prayerRequest->requester_name ?: 'A', 0, 1) }}
                </div>
                <div>
                    <h1 class="text-lg sm:text-xl font-bold text-slate-900 leading-tight">
                        {{ $prayerRequest->requester_name ?: __('Petición Anónima') }}
                    </h1>
                    <p class="text-[11px] text-stone-500">{{ __('Enviada') }} {{ $prayerRequest->created_at->diffForHumans() }}</p>
                </div>
            </div>

            <span class="text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider {{ $prayerRequest->status->badgeColor() }}">
                {{ $prayerRequest->status->label() }}
            </span>
        </div>

        <p class="text-slate-800 text-sm leading-relaxed whitespace-pre-wrap font-serif italic pt-1">
            “{{ $prayerRequest->translated_content }}”
        </p>
    </div>

    <!-- Answered State or Action -->
    @if ($prayerRequest->is_answered)
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4">
            <p class="text-sm font-bold text-emerald-800 flex items-center gap-1.5">
                <span>🙏</span> {{ __('Esta petición fue marcada como respondida / contestada') }}
            </p>
            @if ($prayerRequest->answer_note)
                <p class="text-xs sm:text-sm text-emerald-700 mt-2 whitespace-pre-wrap leading-relaxed">
                    {{ $prayerRequest->translated_answer_note }}
                </p>
            @endif
        </div>
    @else
        <div>
            @if (! $showAnsweredForm)
                <button type="button" wire:click="$set('showAnsweredForm', true)" class="text-xs font-bold uppercase tracking-wider text-amber-600 hover:text-amber-500 transition-colors inline-flex items-center gap-1">
                    <span>{{ __('Marcar petición como contestada') }}</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </button>
            @else
                <div class="bg-stone-50 border border-stone-200 rounded-xl p-4 space-y-3">
                    <x-input-label for="answer_note" :value="__('¿Cómo respondió Dios a tu oración? (Opcional)')" />
                    <textarea wire:model="answer_note" id="answer_note" rows="3" class="block w-full rounded border-stone-300 bg-white shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm p-3 placeholder-stone-400" placeholder="{{ __('Comparte tu testimonio o agradecimiento...') }}"></textarea>
                    <div class="flex gap-2 justify-end">
                        <button type="button" wire:click="$set('showAnsweredForm', false)" class="px-4 py-2 rounded text-xs font-bold uppercase tracking-wider text-stone-600 hover:bg-stone-200 transition-all">
                            {{ __('Cancelar') }}
                        </button>
                        <button type="button" wire:click="markAnswered" class="px-4 py-2 rounded bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider transition-all shadow-sm">
                            {{ __('Confirmar') }}
                        </button>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Public Comments Area (If public) -->
    @if ($prayerRequest->is_public)
        <div class="mt-8">
            <h3 class="text-sm font-bold text-slate-900 mb-4">{{ __('Comentarios Comunitarios') }}</h3>
            <div class="space-y-4">
                @forelse ($prayerRequest->publicComments as $comment)
                    <div class="p-4 rounded-xl border border-stone-200 bg-white shadow-sm">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-bold text-sm text-slate-900">{{ $comment->author_name ?: __('Anónimo') }}</span>
                            <span class="text-[10px] text-stone-400 font-medium">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-slate-700 text-sm whitespace-pre-wrap">{{ $comment->translated_body }}</p>
                    </div>
                @empty
                    <div class="p-4 rounded-xl border border-stone-200 bg-stone-50 text-center">
                        <p class="text-sm text-stone-500">{{ __('Nadie ha dejado un comentario público aún.') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    @else
        <!-- Private Chat Area -->
        <livewire:prayer-chat
            :prayer-request="$prayerRequest"
            :viewer-role="$this->viewerRole"
            :viewer-user-id="auth()->id()"
            :key="'chat-'.$prayerRequest->id"
        />
    @endif
</div>
