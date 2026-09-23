<?php

use App\Enums\UserRole;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] class extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function toggleActive(User $intercessor): void
    {
        abort_if($intercessor->role !== UserRole::Intercessor, 403);

        $intercessor->update(['is_active' => ! $intercessor->is_active]);
    }

    public function getIntercessorsProperty()
    {
        return User::where('role', UserRole::Intercessor)
            ->withCount('assignedPrayerRequests')
            ->when($this->search, fn ($q) => $q->where(function ($sub) {
                $sub->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            }))
            ->orderBy('name')
            ->paginate(15);
    }
}; ?>

<div class="space-y-6">
    <x-slot name="header">
        {{ __('Equipo de Intercesores') }}
    </x-slot>

    <!-- Main Table Container (TailAdmin Style) -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
        
        <!-- Header Toolbar -->
        <div class="p-6 border-b border-stone-200/80 bg-stone-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">{{ __('Intercesores Registrados') }}</h3>
                <p class="text-xs text-stone-500">{{ __('Personas comprometidas a orar por las peticiones') }}</p>
            </div>

            <div class="relative w-full sm:w-72">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-stone-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" 
                       type="text" 
                       placeholder="{{ __('Buscar intercesor por nombre...') }}" 
                       class="w-full bg-white text-xs rounded-xl border border-stone-200 pl-9 pr-3 py-2 focus:border-amber-500 focus:ring-amber-500 placeholder-stone-400 transition-colors">
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-stone-50/80 border-b border-stone-200/80 text-[11px] font-bold uppercase tracking-wider text-stone-500">
                        <th class="py-4 px-6">{{ __('Intercesor') }}</th>
                        <th class="py-4 px-4">{{ __('Correo Electrónico') }}</th>
                        <th class="py-4 px-4 text-center">{{ __('Peticiones Asignadas') }}</th>
                        <th class="py-4 px-4">{{ __('Estado') }}</th>
                        <th class="py-4 px-6 text-right">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-xs">
                    @forelse ($this->intercessors as $intercessor)
                        <tr class="hover:bg-stone-50/60 transition-colors">
                            
                            <!-- Intercesor Avatar & Name -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-slate-950 text-amber-400 font-bold text-xs flex items-center justify-center shrink-0 border border-white/10">
                                        {{ substr($intercessor->name, 0, 1) }}
                                    </div>
                                    <span class="font-bold text-slate-900 text-xs">{{ $intercessor->name }}</span>
                                </div>
                            </td>

                            <!-- Email -->
                            <td class="py-4 px-4 text-stone-600 font-medium">
                                {{ $intercessor->email }}
                            </td>

                            <!-- Peticiones Asignadas -->
                            <td class="py-4 px-4 text-center">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-blue-50 text-blue-700 font-bold text-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                    {{ $intercessor->assigned_prayer_requests_count }}
                                </span>
                            </td>

                            <!-- Estado -->
                            <td class="py-4 px-4">
                                @if ($intercessor->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200/60 text-emerald-700 text-xs font-bold uppercase tracking-wider">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ __('Activo') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-stone-100 text-stone-500 text-xs font-bold uppercase tracking-wider">
                                        <span class="w-1.5 h-1.5 rounded-full bg-stone-400"></span>
                                        {{ __('Inactivo') }}
                                    </span>
                                @endif
                            </td>

                            <!-- Action -->
                            <td class="py-4 px-6 text-right">
                                <button wire:click="toggleActive({{ $intercessor->id }})" 
                                        class="px-3.5 py-1.5 rounded-xl border font-bold text-xs transition-all {{ $intercessor->is_active ? 'border-red-200 text-red-600 hover:bg-red-50' : 'border-emerald-200 text-emerald-700 hover:bg-emerald-50' }}">
                                    {{ $intercessor->is_active ? __('Desactivar') : __('Activar') }}
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-stone-400">
                                <p class="font-bold text-slate-700">{{ __('No hay intercesores registrados') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-stone-100 bg-stone-50/50">
            {{ $this->intercessors->links() }}
        </div>

    </div>
</div>
