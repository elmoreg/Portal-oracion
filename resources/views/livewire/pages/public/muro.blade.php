<?php

use App\Models\PrayerRequest;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.public')] class extends Component {
    public function with(): array
    {
        return [
            'requests' => PrayerRequest::where('is_public', true)
                ->withCount('publicComments')
                ->latest()
                ->paginate(12),
        ];
    }
}; ?>

<div>
    <!-- Header Section -->
    <div class="text-center max-w-3xl mx-auto mb-12">
        <div class="inline-flex items-center justify-center gap-2 mb-3">
            <span class="h-px w-8 bg-amber-500"></span>
            <span class="text-amber-600 font-bold tracking-[0.2em] uppercase text-xs">{{ __('Comunidad en Oración') }}</span>
            <span class="h-px w-8 bg-amber-500"></span>
        </div>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-slate-900 tracking-tight">
            {{ __('Muro de Oración') }}
        </h1>
        <p class="text-stone-600 leading-relaxed text-sm sm:text-base mt-3 font-light">
            {{ __('Acompaña a personas que han decidido compartir su necesidad con la comunidad. Puedes orar por ellas y dejar registrado tu apoyo.') }}
        </p>

        <div class="mt-6">
            <a href="{{ route('prayer.create') }}" wire:navigate 
               class="inline-flex items-center gap-2 px-6 py-3 rounded bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-widest transition-all shadow-md">
                <span>{{ __('Pedir Oración') }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </a>
        </div>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-7xl mx-auto text-start">
        @forelse($requests as $request)
            <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border-t-4 border-amber-500 border-x border-b border-stone-200 p-6 flex flex-col justify-between hover:-translate-y-1">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-4 border-b border-stone-100 pb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-amber-500/20 text-amber-600 flex items-center justify-center font-bold text-xs">
                                {{ substr($request->requester_name ?: 'A', 0, 1) }}
                            </div>
                            <span class="text-xs font-bold text-slate-900">
                                {{ $request->requester_name ?: __('Petición Anónima') }}
                            </span>
                        </div>
                        <span class="text-[11px] text-stone-400 font-medium">
                            {{ $request->created_at->diffForHumans() }}
                        </span>
                    </div>

                    <p class="text-slate-700 text-sm font-serif italic leading-relaxed mb-6 whitespace-pre-wrap">
                        “{{ $request->translated_content }}”
                    </p>
                </div>

                <div class="pt-4 border-t border-stone-100 flex items-center justify-between mt-auto">
                    <div class="flex items-center gap-4">
                        <span class="text-[11px] text-amber-600 font-medium flex items-center gap-1">
                            <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"></path></svg>
                            <span>{{ $request->prayed_count }}</span>
                        </span>
                        <a href="{{ route('prayer.pray', $request) }}#comments" wire:navigate class="text-[11px] text-stone-500 hover:text-amber-600 font-medium flex items-center gap-1 transition-colors" title="Ver comentarios">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            <span>{{ $request->public_comments_count }}</span>
                        </a>
                    </div>
                    <a href="{{ route('prayer.pray', $request) }}" wire:navigate 
                       class="px-3.5 py-1.5 rounded bg-slate-950 hover:bg-amber-500 text-white hover:text-slate-950 text-xs font-bold uppercase tracking-wider transition-all">
                        {{ __('Orar') }} &rarr;
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-stone-200 p-12 text-center shadow-sm">
                <div class="w-16 h-16 bg-amber-500/10 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">{{ __('Muro de Oraciones Públicas') }}</h3>
                <p class="text-stone-500 text-sm max-w-md mx-auto mb-6">{{ __('Acompaña a personas que han decidido compartir su necesidad con la comunidad. Puedes orar por ellas y dejar registrado tu apoyo.') }}</p>
                <a href="{{ route('prayer.create') }}" wire:navigate class="inline-flex items-center px-6 py-3 rounded bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-widest transition-all">
                    {{ __('Pedir Oración') }}
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-12 max-w-7xl mx-auto">
        {{ $requests->links() }}
    </div>
</div>
