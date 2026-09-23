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
        $this->dispatch('open-modal', 'user-form');
    }

    public function openEditModal(int|User $user): void
    {
        $userModel = $user instanceof User ? $user : User::findOrFail($user);
        $this->resetFormFields();
        $this->isEditing = true;
        $this->editingUserId = $userModel->id;
        $this->formName = $userModel->name;
        $this->formEmail = $userModel->email;
        $this->formRole = $userModel->role->value;
        $this->showingFormModal = true;
        $this->dispatch('open-modal', 'user-form');
    }

    public function closeModal(): void
    {
        $this->showingFormModal = false;
        $this->confirmingDeletion = false;
        $this->dispatch('close-modal', 'user-form');
        $this->dispatch('close-modal', 'confirm-delete');
        $this->resetFormFields();
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
            'email' => strtolower(trim($validated['formEmail'])),
            'password' => $validated['formPassword'],
            'role' => $validated['formRole'],
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->closeModal();
    }

    public function updateUser(): void
    {
        $rules = [
            'formName' => ['required', 'string', 'max:255'],
            'formEmail' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->editingUserId)],
            'formRole' => ['required', Rule::enum(UserRole::class)],
        ];

        if (! empty($this->formPassword)) {
            $rules['formPassword'] = ['string', 'min:8', 'confirmed'];
        }

        $validated = $this->validate($rules);

        $user = User::findOrFail($this->editingUserId);

        $data = [
            'name' => $validated['formName'],
            'email' => strtolower(trim($validated['formEmail'])),
            'role' => $validated['formRole'],
        ];

        if (! empty($this->formPassword)) {
            $data['password'] = $this->formPassword;
        }

        $user->update($data);

        $this->closeModal();
    }

    public function toggleActive(int|User $user): void
    {
        $userId = $user instanceof User ? $user->id : $user;
        abort_if($userId === auth()->id(), 403, 'No puedes desactivar tu propia cuenta.');

        $userModel = $user instanceof User ? $user : User::findOrFail($userId);
        $userModel->update(['is_active' => ! $userModel->is_active]);
    }

    public function confirmDelete(int|User $user): void
    {
        $this->deletingUserId = $user instanceof User ? $user->id : $user;
        $this->confirmingDeletion = true;
        $this->dispatch('open-modal', 'confirm-delete');
    }

    public function deleteUser(): void
    {
        abort_if($this->deletingUserId === auth()->id(), 403, 'No puedes eliminar tu propia cuenta.');

        User::findOrFail($this->deletingUserId)->delete();

        $this->closeModal();
    }

    private function resetFormFields(): void
    {
        $this->formName = '';
        $this->formEmail = '';
        $this->formPassword = '';
        $this->formPassword_confirmation = '';
        $this->formRole = 'intercessor';
        $this->editingUserId = null;
        $this->deletingUserId = null;
        $this->resetValidation();
    }
}; ?>

<div class="space-y-6">
    <x-slot name="header">
        {{ __('Gestión de Cuentas y Usuarios') }}
    </x-slot>

    <!-- Main Table Card Container (TailAdmin Style) -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
        
        <!-- Header Toolbar -->
        <div class="p-6 border-b border-stone-200/80 bg-stone-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            
            <!-- Left: Search Box -->
            <div class="relative w-full sm:w-72">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-stone-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" 
                       type="text" 
                       placeholder="{{ __('Buscar usuario o correo...') }}" 
                       class="w-full bg-white text-xs rounded-xl border border-stone-200 pl-9 pr-3 py-2.5 focus:border-amber-500 focus:ring-amber-500 placeholder-stone-400 transition-colors">
            </div>

            <!-- Right: Filters & New User Button -->
            <div class="flex items-center gap-3 flex-wrap">
                <!-- Role Filter -->
                <select wire:model.live="roleFilter" class="bg-white text-xs font-semibold text-slate-700 rounded-xl border border-stone-200 px-3 py-2.5 focus:border-amber-500 focus:ring-amber-500">
                    <option value="">{{ __('Todos los roles') }}</option>
                    @foreach (UserRole::cases() as $case)
                        <option value="{{ $case->value }}">{{ $case->label() }}</option>
                    @endforeach
                </select>

                <!-- Status Filter -->
                <select wire:model.live="statusFilter" class="bg-white text-xs font-semibold text-slate-700 rounded-xl border border-stone-200 px-3 py-2.5 focus:border-amber-500 focus:ring-amber-500">
                    <option value="">{{ __('Todos los estados') }}</option>
                    <option value="1">{{ __('Activo') }}</option>
                    <option value="0">{{ __('Inactivo') }}</option>
                </select>

                <button wire:click="openCreateModal" 
                        class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider transition-all inline-flex items-center gap-2 shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>{{ __('Nuevo Usuario') }}</span>
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-stone-50/80 border-b border-stone-200/80 text-[11px] font-bold uppercase tracking-wider text-stone-500">
                        <th class="py-4 px-6">{{ __('Usuario') }}</th>
                        <th class="py-4 px-4">{{ __('Correo Electrónico') }}</th>
                        <th class="py-4 px-4">{{ __('Rol') }}</th>
                        <th class="py-4 px-4">{{ __('Estado') }}</th>
                        <th class="py-4 px-4">{{ __('Registro') }}</th>
                        <th class="py-4 px-6 text-right">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-xs">
                    @forelse ($this->users as $user)
                        <tr wire:key="user-{{ $user->id }}" class="hover:bg-stone-50/60 transition-colors">
                            
                            <!-- User Avatar & Name -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-slate-950 text-amber-400 font-bold text-xs flex items-center justify-center shrink-0 border border-white/10">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <span class="font-bold text-slate-900 text-xs">{{ $user->name }}</span>
                                </div>
                            </td>

                            <!-- Email -->
                            <td class="py-4 px-4 text-stone-600 font-medium">
                                {{ $user->email }}
                            </td>

                            <!-- Role -->
                            <td class="py-4 px-4">
                                @if ($user->role === UserRole::Admin)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 border border-amber-200/60 text-amber-700 text-xs font-bold uppercase tracking-wider">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        {{ $user->role->label() }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 border border-blue-200/60 text-blue-700 text-xs font-bold uppercase tracking-wider">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        {{ $user->role->label() }}
                                    </span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-4">
                                @if ($user->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ __('Activo') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-stone-100 text-stone-500 text-xs font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-stone-400"></span>
                                        {{ __('Inactivo') }}
                                    </span>
                                @endif
                            </td>

                            <!-- Registration Date -->
                            <td class="py-4 px-4 text-stone-400 text-[11px] font-medium">
                                {{ $user->created_at->diffForHumans() }}
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-6 text-right whitespace-nowrap space-x-2">
                                <button wire:click="openEditModal({{ $user->id }})" 
                                        class="text-xs font-bold text-slate-800 hover:text-amber-600 transition-colors">
                                    {{ __('Editar') }}
                                </button>

                                @if ($user->id !== auth()->id())
                                    <button wire:click="toggleActive({{ $user->id }})" 
                                            class="text-xs font-bold {{ $user->is_active ? 'text-stone-500 hover:text-slate-800' : 'text-emerald-700 hover:text-emerald-800' }} transition-colors">
                                        {{ $user->is_active ? __('Desactivar') : __('Activar') }}
                                    </button>

                                    <button wire:click="confirmDelete({{ $user->id }})" 
                                            class="text-xs font-bold text-red-600 hover:text-red-800 transition-colors">
                                        {{ __('Eliminar') }}
                                    </button>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-stone-400">
                                <p class="font-bold text-slate-700">{{ __('No se encontraron usuarios') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-stone-100 bg-stone-50/50">
            {{ $this->users->links() }}
        </div>

    </div>

    <!-- Modal Crear / Editar (TailAdmin Style) -->
    <x-modal name="user-form" :show="$showingFormModal" maxWidth="lg">
        <form wire:submit="saveUser" class="p-8">
            <h3 class="text-xl font-bold text-slate-900 mb-6">
                {{ $isEditing ? __('Editar Usuario') : __('Crear Nuevo Usuario') }}
            </h3>

            <div class="space-y-4">
                <div>
                    <x-input-label for="formName" :value="__('Nombre Completo')" />
                    <x-text-input wire:model="formName" id="formName" type="text" class="mt-1 block w-full" required placeholder="Nombre y apellido" />
                    <x-input-error :messages="$errors->get('formName')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="formEmail" :value="__('Correo Electrónico')" />
                    <x-text-input wire:model="formEmail" id="formEmail" type="email" class="mt-1 block w-full" required placeholder="usuario@correo.com" />
                    <x-input-error :messages="$errors->get('formEmail')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="formPassword" :value="$isEditing ? __('Nueva Contraseña (opcional)') : __('Contraseña')" />
                    <x-text-input wire:model="formPassword" id="formPassword" type="password" class="mt-1 block w-full" :required="!$isEditing" :placeholder="$isEditing ? __('Dejar en blanco para mantener la actual') : __('Mínimo 8 caracteres')" />
                    <x-input-error :messages="$errors->get('formPassword')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="formPassword_confirmation" :value="$isEditing ? __('Confirmar Nueva Contraseña') : __('Confirmar Contraseña')" />
                    <x-text-input wire:model="formPassword_confirmation" id="formPassword_confirmation" type="password" class="mt-1 block w-full" :required="!$isEditing && !empty($formPassword)" :placeholder="$isEditing ? __('Repite la nueva contraseña') : __('Repite la contraseña')" />
                </div>

                <div>
                    <x-input-label for="formRole" :value="__('Rol en el Portal')" />
                    <select wire:model="formRole" id="formRole" class="mt-1 block w-full rounded-xl border border-stone-200 text-xs font-semibold text-slate-800 p-3 focus:border-amber-500 focus:ring-amber-500">
                        @foreach (UserRole::cases() as $case)
                            <option value="{{ $case->value }}">{{ $case->label() }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('formRole')" class="mt-1" />
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button type="button" wire:click="closeModal" x-on:click="$dispatch('close')" 
                        class="px-5 py-2.5 rounded-xl border border-stone-300 text-xs font-bold uppercase tracking-wider text-stone-600 hover:bg-stone-50 transition-all">
                    {{ __('Cancelar') }}
                </button>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider transition-all shadow-xs">
                    {{ $isEditing ? __('Guardar Cambios') : __('Crear Usuario') }}
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Modal Confirmar Eliminación -->
    <x-modal name="confirm-delete" :show="$confirmingDeletion" maxWidth="md">
        <div class="p-8">
            <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">{{ __('¿Eliminar este usuario?') }}</h3>
            <p class="text-xs text-stone-500 mb-6 leading-relaxed">{{ __('Esta acción es permanente e irreversible. El usuario perderá el acceso al portal.') }}</p>

            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeModal" x-on:click="$dispatch('close')" 
                        class="px-4 py-2 rounded-xl border border-stone-300 text-xs font-bold text-stone-600 hover:bg-stone-50 transition-all">
                    {{ __('Cancelar') }}
                </button>
                <button type="button" wire:click="deleteUser" 
                        class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition-all shadow-xs">
                    {{ __('Sí, eliminar') }}
                </button>
            </div>
        </div>
    </x-modal>
</div>
