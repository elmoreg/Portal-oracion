<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] class extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $roleFilter = '';

    #[Url]
    public string $statusFilter = '';

    /** Modal de crear/editar */
    public bool $showingFormModal = false;

    public bool $isEditing = false;

    public ?int $editingUserId = null;

    public string $formName = '';

    public string $formEmail = '';

    public string $formPassword = '';

    public string $formPassword_confirmation = '';

    public string $formRole = 'intercessor';

    /** Modal de confirmación para eliminar */
    public bool $confirmingDeletion = false;

    public ?int $deletingUserId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function getUsersProperty()
    {
        return User::query()
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->when($this->roleFilter, fn ($q) => $q->where('role', $this->roleFilter))
            ->when($this->statusFilter !== '', function ($q) {
                if ($this->statusFilter === '1') {
                    $q->where('is_active', true);
                } elseif ($this->statusFilter === '0') {
                    $q->where('is_active', false);
                }
            })
            ->orderBy('name')
            ->paginate(15);
    }

    public function openCreateModal(): void
    {
        $this->resetFormFields();
        $this->isEditing = false;
        $this->showingFormModal = true;
    }

    public function openEditModal(User $user): void
    {
        $this->resetFormFields();
        $this->isEditing = true;
        $this->editingUserId = $user->id;
        $this->formName = $user->name;
        $this->formEmail = $user->email;
        $this->formRole = $user->role->value;
        $this->showingFormModal = true;
    }

    public function saveUser(): void
    {
        if ($this->isEditing) {
            $this->updateUser();
        } else {
            $this->createUser();
        }
    }

    public function createUser(): void
    {
        $validated = $this->validate([
            'formName' => ['required', 'string', 'max:255'],
            'formEmail' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'formPassword' => ['required', 'string', 'min:8', 'confirmed'],
            'formRole' => ['required', Rule::enum(UserRole::class)],
        ]);

        User::create([
            'name' => $validated['formName'],
            'email' => $validated['formEmail'],
            'password' => $validated['formPassword'],
            'role' => $validated['formRole'],
        ]);

        $this->showingFormModal = false;
        $this->resetFormFields();
    }

    public function updateUser(): void
    {
        $validated = $this->validate([
            'formName' => ['required', 'string', 'max:255'],
            'formEmail' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->editingUserId)],
            'formRole' => ['required', Rule::enum(UserRole::class)],
        ]);

        $user = User::findOrFail($this->editingUserId);

        $user->update([
            'name' => $validated['formName'],
            'email' => $validated['formEmail'],
            'role' => $validated['formRole'],
        ]);

        $this->showingFormModal = false;
        $this->resetFormFields();
    }

    public function toggleActive(User $user): void
    {
        abort_if($user->id === auth()->id(), 403, 'No podés desactivar tu propia cuenta.');

        $user->update(['is_active' => ! $user->is_active]);
    }

    public function confirmDelete(int $userId): void
    {
        $this->deletingUserId = $userId;
        $this->confirmingDeletion = true;
    }

    public function deleteUser(): void
    {
        abort_if($this->deletingUserId === auth()->id(), 403, 'No podés eliminar tu propia cuenta.');

        User::findOrFail($this->deletingUserId)->delete();

        $this->confirmingDeletion = false;
        $this->deletingUserId = null;
    }

    private function resetFormFields(): void
    {
        $this->formName = '';
        $this->formEmail = '';
        $this->formPassword = '';
        $this->formPassword_confirmation = '';
        $this->formRole = 'intercessor';
        $this->editingUserId = null;
        $this->resetValidation();
    }
}; ?>

<div>
    <x-slot name="header">
        Gestión de usuarios
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Header con título y botón --}}
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Usuarios del portal</h2>
                <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-soul-indigo text-white text-sm font-medium rounded-lg hover:bg-soul-indigo/90 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Nuevo usuario
                </button>
            </div>

            {{-- Filtros --}}
            <div class="flex gap-3 flex-wrap">
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Buscar por nombre o email…"
                    class="rounded-md border-gray-300 text-sm w-64"
                />

                <select wire:model.live="roleFilter" class="rounded-md border-gray-300 text-sm">
                    <option value="">Todos los roles</option>
                    @foreach (UserRole::cases() as $case)
                        <option value="{{ $case->value }}">{{ $case->label() }}</option>
                    @endforeach
                </select>

                <select wire:model.live="statusFilter" class="rounded-md border-gray-300 text-sm">
                    <option value="">Todos los estados</option>
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
            </div>

            {{-- Tabla --}}
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs text-gray-500 uppercase">
                        <tr>
                            <th class="px-4 py-2">Nombre</th>
                            <th class="px-4 py-2">Email</th>
                            <th class="px-4 py-2">Rol</th>
                            <th class="px-4 py-2">Estado</th>
                            <th class="px-4 py-2">Registro</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($this->users as $user)
                            <tr wire:key="user-{{ $user->id }}">
                                <td class="px-4 py-3 text-gray-700 font-medium">{{ $user->name }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $user->email }}</td>
                                <td class="px-4 py-3">
                                    @if ($user->role === UserRole::Admin)
                                        <span class="text-xs font-medium px-2 py-1 rounded-full bg-purple-100 text-purple-800">{{ $user->role->label() }}</span>
                                    @else
                                        <span class="text-xs font-medium px-2 py-1 rounded-full bg-blue-100 text-blue-800">{{ $user->role->label() }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($user->is_active)
                                        <span class="text-xs font-medium px-2 py-1 rounded-full bg-green-100 text-green-800">Activo</span>
                                    @else
                                        <span class="text-xs font-medium px-2 py-1 rounded-full bg-gray-100 text-gray-600">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-400">{{ $user->created_at->diffForHumans() }}</td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <button wire:click="openEditModal({{ $user->id }})" class="text-indigo-600 hover:text-indigo-800 transition-colors">
                                        Editar
                                    </button>

                                    @if ($user->id !== auth()->id())
                                        <button wire:click="toggleActive({{ $user->id }})" class="text-amber-600 hover:text-amber-800 transition-colors">
                                            {{ $user->is_active ? 'Desactivar' : 'Activar' }}
                                        </button>

                                        <button wire:click="confirmDelete({{ $user->id }})" class="text-red-600 hover:text-red-800 transition-colors">
                                            Eliminar
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-400">No hay usuarios con esos filtros.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $this->users->links() }}
        </div>
    </div>

    {{-- Modal Crear / Editar --}}
    <x-modal name="user-form" :show="$showingFormModal" maxWidth="lg">
        <form wire:submit="saveUser" class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">
                {{ $isEditing ? 'Editar usuario' : 'Nuevo usuario' }}
            </h3>

            <div class="space-y-4">
                <div>
                    <x-input-label for="formName" value="Nombre" />
                    <x-text-input wire:model="formName" id="formName" type="text" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('formName')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="formEmail" value="Correo electrónico" />
                    <x-text-input wire:model="formEmail" id="formEmail" type="email" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('formEmail')" class="mt-1" />
                </div>

                @unless ($isEditing)
                    <div>
                        <x-input-label for="formPassword" value="Contraseña" />
                        <x-text-input wire:model="formPassword" id="formPassword" type="password" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('formPassword')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="formPassword_confirmation" value="Confirmar contraseña" />
                        <x-text-input wire:model="formPassword_confirmation" id="formPassword_confirmation" type="password" class="mt-1 block w-full" required />
                    </div>
                @endunless

                <div>
                    <x-input-label for="formRole" value="Rol" />
                    <select wire:model="formRole" id="formRole" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach (UserRole::cases() as $case)
                            <option value="{{ $case->value }}">{{ $case->label() }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('formRole')" class="mt-1" />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button wire:click="$set('showingFormModal', false)">
                    Cancelar
                </x-secondary-button>
                <x-primary-button type="submit">
                    {{ $isEditing ? 'Guardar cambios' : 'Crear usuario' }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    {{-- Modal Confirmar Eliminación --}}
    <x-modal name="confirm-delete" :show="$confirmingDeletion" maxWidth="md">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">¿Eliminar este usuario?</h3>
            <p class="text-sm text-gray-600 mb-6">Esta acción es irreversible. El usuario será eliminado permanentemente del sistema.</p>

            <div class="flex justify-end gap-3">
                <x-secondary-button wire:click="$set('confirmingDeletion', false)">
                    Cancelar
                </x-secondary-button>
                <x-danger-button wire:click="deleteUser">
                    Sí, eliminar
                </x-danger-button>
            </div>
        </div>
    </x-modal>
</div>
