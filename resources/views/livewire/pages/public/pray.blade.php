<?php

use App\Models\PrayerRequest;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Mail;

new #[Layout('layouts.public')] class extends Component {
    public PrayerRequest $prayerRequest;

    public function mount(PrayerRequest $prayerRequest): void
    {
        // Solo para peticiones públicas
        abort_unless($prayerRequest->is_public, 404);

        $this->prayerRequest = $prayerRequest;

        // Evitar múltiples incrementos por recargas en la misma sesión
        $sessionKey = 'prayed_for_' . $prayerRequest->id;
        if (! session()->has($sessionKey)) {
            $this->prayerRequest->increment('prayed_count');
            session()->put($sessionKey, true);
            
            if ($this->prayerRequest->email) {
                Mail::to($this->prayerRequest->email)->send(new \App\Mail\SomeonePrayedForYou($this->prayerRequest));
            }
        }
    }

    public string $comment_author = '';
    public string $comment_body = '';

    public function submitComment(): void
    {
        $this->validate([
            'comment_author' => ['required', 'string', 'max:100'],
            'comment_body' => ['required', 'string', 'min:3', 'max:1000'],
        ], [
            'comment_author.required' => __('Por favor, escribe tu nombre.'),
        ]);

        $this->prayerRequest->publicComments()->create([
            'author_name' => trim($this->comment_author),
            'body' => $this->comment_body,
        ]);

        $this->comment_author = '';
        $this->comment_body = '';
        $this->dispatch('comment-added');
    }
}; ?>

@push('meta')
    @php
        $shareTitle = $prayerRequest->requester_name
            ? __(':name pide tu oración', ['name' => $prayerRequest->requester_name])
            : __('Alguien pide tu oración');
        $shareDescription = \Illuminate\Support\Str::limit(
            trim(preg_replace('/\s+/', ' ', (string) $prayerRequest->translated_content)),
            160,
            '…'
        );
        $shareImage = route('prayer.share-image', $prayerRequest);
        $shareUrl = route('prayer.pray', $prayerRequest);
    @endphp
    <title>{{ $shareTitle }} · {{ __('Portal de Oración') }}</title>
    <meta name="description" content="{{ $shareDescription }}">
    <meta property="og:site_name" content="{{ __('Portal de Oración') }}">
    <meta property="og:title" content="{{ $shareTitle }}">
    <meta property="og:description" content="{{ $shareDescription }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ $shareUrl }}">
    <meta property="og:image" content="{{ $shareImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $shareTitle }}">
    <meta name="twitter:description" content="{{ $shareDescription }}">
    <meta name="twitter:image" content="{{ $shareImage }}">
@endpush

<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden border-t-4 border-amber-500 border-x border-b border-stone-200">
        <!-- Header Banner -->
        <div class="bg-slate-950 px-8 py-12 text-center relative overflow-hidden text-white">
            <div class="absolute inset-0 bg-gradient-to-b from-amber-500/10 via-transparent to-slate-950"></div>
            
            <div class="relative z-10">
                <div class="w-16 h-16 bg-amber-500/20 rounded-full flex items-center justify-center mx-auto mb-4 border border-amber-400/40 text-amber-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </div>
                
                <span class="text-[10px] font-bold text-amber-400 uppercase tracking-[0.25em] mb-2 block">{{ __('Intercesión Comunitaria') }}</span>
                <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">{{ __('¡Gracias por unirte en oración!') }}</h1>
                <p class="text-stone-300 text-xs sm:text-sm font-light max-w-md mx-auto">{{ __('Tu apoyo y oración marcan una diferencia espiritual en este momento de necesidad.') }}</p>
            </div>
        </div>

        <div class="px-6 sm:px-10 py-8 space-y-8 text-start">
            <!-- The Request content -->
            <div class="p-6 bg-stone-50 rounded-xl border border-stone-200">
                <div class="flex items-center justify-between mb-3 border-b border-stone-200/80 pb-2">
                    <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">
                        {{ __('Petición de') }} {{ $prayerRequest->requester_name ?: __('Alguien anónimo') }}
                    </span>
                    <span class="text-xs text-stone-400 font-medium">
                        {{ $prayerRequest->prayed_count }} {{ $prayerRequest->prayed_count === 1 ? __('persona ha orado') : __('personas han orado') }}
                    </span>
                </div>
                <p class="text-slate-800 whitespace-pre-wrap font-serif italic text-base leading-relaxed">
                    “{{ $prayerRequest->translated_content }}”
                </p>
            </div>

            <!-- Compartir en redes sociales -->
            <x-share-buttons
                :url="route('prayer.pray', $prayerRequest)"
                :text="__('Únete a orar por esta petición 🙏')"
                :image-square="route('prayer.share-image', ['prayerRequest' => $prayerRequest, 'formato' => 'square'])"
                :image-story="route('prayer.share-image', ['prayerRequest' => $prayerRequest, 'formato' => 'story'])"
            />

            <!-- Comments Section (right below the request) -->
            <div id="comments" class="border-t border-stone-200 pt-8">
                <h3 class="text-lg font-bold text-slate-900 mb-6">{{ __('Comentarios y Palabras de Aliento') }}</h3>

                <!-- Comments list first, so they don't get lost below the form -->
                <div class="space-y-4 mb-8">
                    @forelse ($prayerRequest->publicComments as $comment)
                        <div class="p-4 rounded-xl border border-stone-100 bg-white shadow-sm">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-bold text-sm text-slate-900">{{ $comment->author_name ?: __('Anónimo') }}</span>
                                <span class="text-[10px] text-stone-400 font-medium">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-slate-700 text-sm whitespace-pre-wrap">{{ $comment->translated_body }}</p>
                        </div>
                    @empty
                        <div class="text-center py-6 px-4 bg-stone-50 rounded-xl border border-stone-200">
                            <p class="text-sm text-stone-500">{{ __('Aún no hay comentarios. Sé el primero en dejar una palabra de aliento.') }}</p>
                        </div>
                    @endforelse
                </div>

                <!-- Comment form after the list -->
                <form wire:submit="submitComment" class="bg-stone-50 p-5 rounded-xl border border-stone-200">
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="comment_author" :value="__('Tu nombre')" />
                            <x-text-input wire:model="comment_author" id="comment_author" type="text" required maxlength="100" class="block w-full text-sm py-2" :placeholder="__('Escribe tu nombre')" />
                            <x-input-error :messages="$errors->get('comment_author')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="comment_body" :value="__('Mensaje')" />
                            <textarea wire:model="comment_body" id="comment_body" rows="3" required class="block w-full rounded border-stone-300 bg-white shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm p-3" placeholder="Deja un mensaje de ánimo, una promesa bíblica o una palabra de fe..."></textarea>
                            <x-input-error :messages="$errors->get('comment_body')" class="mt-1" />
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider rounded shadow-sm transition-all" wire:loading.class="opacity-75">
                                <span wire:loading.remove wire:target="submitComment">{{ __('Publicar comentario') }}</span>
                                <span wire:loading wire:target="submitComment">{{ __('Publicando...') }}</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Scripture Note -->
            <div class="text-center py-4 px-6 rounded-xl bg-amber-500/5 border border-amber-500/20 space-y-3">
                <span class="text-[10px] font-bold text-amber-600 tracking-[0.2em] uppercase block">{{ __('Palabra de Aliento') }}</span>
                <blockquote class="text-lg sm:text-xl font-serif italic text-slate-900 leading-relaxed max-w-lg mx-auto">
                    {{ __('«Porque donde están dos o tres congregados en mi nombre, allí estoy yo en medio de ellos.»') }}
                </blockquote>
                <cite class="text-xs font-bold text-amber-600 tracking-widest not-italic block">{{ __('— MATEO 18:20') }}</cite>
            </div>
            
            <!-- Information note -->
            <div class="flex items-start gap-4 p-4 rounded-xl bg-stone-50 border border-stone-200">
                <div class="mt-0.5 text-amber-500 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-900 mb-1">{{ __('Tu oración ha quedado registrada') }}</h4>
                    <p class="text-xs text-stone-600 leading-relaxed font-light">
                        {{ __('El contador de oraciones de esta petición se ha actualizado para que la persona vea que su comunidad se encuentra intercediendo por ella.') }}
                    </p>
                </div>
            </div>
            
            <!-- Bottom Navigation -->
            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('prayer.muro') }}" wire:navigate 
                   class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3.5 rounded bg-slate-950 hover:bg-amber-500 text-white hover:text-slate-950 font-bold text-xs uppercase tracking-widest transition-all">
                    <span>{{ __('Volver al Muro de Oración') }}</span>
                </a>
                <a href="{{ route('prayer.create') }}" wire:navigate 
                   class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3.5 rounded border border-amber-500/50 text-amber-600 hover:bg-amber-50 font-bold text-xs uppercase tracking-widest transition-all">
                    <span>{{ __('Pedir Oración') }}</span>
                </a>
            </div>
        </div>
    </div>
</div>
