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

<div class="space-y-6">
    
    <!-- Top Bar with Back Link -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.prayer-requests.index') }}" wire:navigate 
           class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-stone-500 hover:text-slate-950 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>{{ __('Volver al listado') }}</span>
        </a>

        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-stone-500 uppercase">{{ __('Estado') }}:</span>
            <select wire:model="status" wire:change="updateStatus" 
                    class="bg-white text-xs font-bold text-slate-800 rounded-xl border border-stone-200 px-3 py-1.5 focus:border-amber-500 focus:ring-amber-500">
                @foreach (PrayerRequestStatus::cases() as $case)
                    <option value="{{ $case->value }}">{{ $case->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Main 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Details & Content (7 cols) -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Request Card -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-stone-200/80 shadow-xs space-y-6">
                
                <!-- Card Header -->
                <div class="flex items-start justify-between gap-4 border-b border-stone-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-amber-500/10 text-amber-700 font-bold text-sm flex items-center justify-center border border-amber-500/20">
                            {{ substr($prayerRequest->requester_name ?: 'A', 0, 1) }}
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 leading-tight">
                                {{ $prayerRequest->requester_name ?: __('Petición Anónima') }}
                            </h2>
                            <p class="text-xs text-stone-400 mt-0.5 font-medium">
                                {{ $prayerRequest->email ?: __('Sin correo registrado') }}
                            </p>
                        </div>
                    </div>

                    <span class="text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider {{ $prayerRequest->status->badgeColor() }}">
                        {{ $prayerRequest->status->label() }}
                    </span>
                </div>

                <!-- Meta Pills -->
                <div class="flex items-center gap-3 flex-wrap text-xs text-stone-500">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-stone-100 font-medium">
                        <svg class="w-3.5 h-3.5 text-stone-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>{{ $prayerRequest->country_name ?? __('Zona desconocida') }}</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-stone-100 font-medium">
                        <svg class="w-3.5 h-3.5 text-stone-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ $prayerRequest->created_at->format('d/m/Y H:i') }} ({{ $prayerRequest->created_at->diffForHumans() }})</span>
                    </span>
                    @if($prayerRequest->is_public)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 font-bold uppercase text-[10px]">
                            <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                            <span>{{ __('Muro Comunitario') }}</span>
                        </span>
                    @endif
                </div>

                <!-- Body Content -->
                <div class="p-5 rounded-xl bg-stone-50 border border-stone-200/80">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block mb-2">{{ __('Motivo de Oración') }}</span>
                    <p class="text-slate-800 text-sm sm:text-base leading-relaxed italic font-serif whitespace-pre-wrap">
                        “{{ $prayerRequest->translated_content }}”
                    </p>
                </div>

                <!-- Answered Section (if answered) -->
                @if ($prayerRequest->is_answered)
                    <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4">
                        <p class="text-xs font-bold text-emerald-800 flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ __('Petición marcada como contestada') }}</span>
                        </p>
                        @if ($prayerRequest->answer_note)
                            <p class="text-xs text-emerald-700 mt-1.5 whitespace-pre-wrap leading-relaxed">
                                {{ $prayerRequest->translated_answer_note }}
                            </p>
                        @endif
                    </div>
                @endif

            </div>

            <!-- Chat Section -->
            @if ($prayerRequest->is_public)
                <div class="bg-white rounded-2xl p-6 border border-stone-200/80 shadow-xs">
                    <h3 class="text-sm font-bold text-slate-900 mb-4">{{ __('Comentarios en el Muro Comunitario') }}</h3>
                    <div class="space-y-3">
                        @forelse ($prayerRequest->publicComments ?? [] as $comment)
                            <div class="p-3.5 rounded-xl border border-stone-100 bg-stone-50">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="font-bold text-xs text-slate-800">{{ $comment->author_name ?: __('Anónimo') }}</span>
                                    <span class="text-[11px] text-stone-400">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-stone-700 text-xs whitespace-pre-wrap">{{ $comment->translated_body }}</p>
                            </div>
                        @empty
                            <p class="text-xs text-stone-400 text-center py-4">{{ __('Aún no hay comentarios en esta petición.') }}</p>
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

        <!-- Right: Intercessors Assignment Card (5 cols) -->
        <div class="lg:col-span-5 space-y-6">
            
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-stone-200/80 shadow-xs">
                <div class="flex items-center justify-between mb-4 border-b border-stone-100 pb-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">{{ __('Asignar Intercesores') }}</h3>
                        <p class="text-xs text-stone-500">{{ __('Selecciona a los miembros del equipo') }}</p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 font-bold text-xs">
                        {{ count($this->selectedIntercessors) }} {{ __('seleccionados') }}
                    </span>
                </div>

                <div class="space-y-2 max-h-80 overflow-y-auto pr-1">
                    @forelse ($this->intercessors as $intercessor)
                        <label class="flex items-center justify-between p-3 rounded-xl border border-stone-100 hover:bg-stone-50 cursor-pointer transition-colors">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" 
                                       wire:model="selectedIntercessors" 
                                       value="{{ $intercessor->id }}" 
                                       class="w-4 h-4 rounded text-amber-500 border-stone-300 focus:ring-amber-500">
                                <div class="w-8 h-8 rounded-full bg-slate-950 text-amber-400 font-bold text-xs flex items-center justify-center shrink-0">
                                    {{ substr($intercessor->name, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 truncate">{{ $intercessor->name }}</p>
                                    <p class="text-[11px] text-stone-400 truncate">{{ $intercessor->email }}</p>
                                </div>
                            </div>
                        </label>
                    @empty
                        <div class="text-center py-6 text-xs text-stone-400">
                            {{ __('No hay intercesores activos registrados.') }}
                        </div>
                    @endforelse
                </div>

                <div class="mt-6 pt-4 border-t border-stone-100 flex items-center justify-between">
                    <button type="button" 
                            wire:click="saveAssignments" 
                            class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider transition-all shadow-xs">
                        {{ __('Guardar Asignación') }}
                    </button>

                    <span wire:loading.remove wire:target="saveAssignments" 
                          x-data="{ shown: false }" 
                          x-init="Livewire.on('assignments-saved', () => { shown = true; setTimeout(() => shown = false, 2500) })" 
                          x-show="shown" 
                          class="text-xs font-bold text-emerald-600 inline-flex items-center gap-1" 
                          style="display: none;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <span>{{ __('Guardado exitosamente') }}</span>
                    </span>
                </div>
            </div>

        </div>

    </div>

</div>
