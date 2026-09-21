<?php

use App\Enums\UserRole;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] class extends Component
{
    use WithPagination;

    public function toggleActive(User $intercessor): void
    {
        abort_if($intercessor->role !== UserRole::Intercessor, 403);

        $intercessor->update(['is_active' => ! $intercessor->is_active]);
    }

    public function getIntercessorsProperty()
    {
        return User::where('role', UserRole::Intercessor)
            ->withCount('assignedPrayerRequests')
            ->orderBy('name')
            ->paginate(20);
    }
}; ?>

<div>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Personas inscritas para orar</h2>

            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs text-gray-500 uppercase">
                        <tr>
                            <th class="px-4 py-2">Nombre</th>
                            <th class="px-4 py-2">Email</th>
                            <th class="px-4 py-2">Asignadas</th>
                            <th class="px-4 py-2">Estado</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($this->intercessors as $intercessor)
                            <tr>
                                <td class="px-4 py-3 text-gray-700">{{ $intercessor->name }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $intercessor->email }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $intercessor->assigned_prayer_requests_count }}</td>
                                <td class="px-4 py-3">
                                    @if ($intercessor->is_active)
                                        <span class="text-xs font-medium px-2 py-1 rounded-full bg-green-100 text-green-800">Activo</span>
                                    @else
                                        <span class="text-xs font-medium px-2 py-1 rounded-full bg-gray-100 text-gray-600">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button wire:click="toggleActive({{ $intercessor->id }})" class="text-indigo-600">
                                        {{ $intercessor->is_active ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-400">Todavía no hay personas inscritas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $this->intercessors->links() }}
        </div>
    </div>
</div>
