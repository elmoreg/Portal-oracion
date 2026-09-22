<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $isRegistered = false;

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'password' => $validated['password'],
            'role' => UserRole::Intercessor,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        event(new Registered($user));

        Auth::login($user);

        $this->isRegistered = true;
    }
}; ?>

<div>
    @if ($isRegistered)
        <div class="text-center py-4 space-y-6">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-500/10 text-emerald-600 mb-1 ring-8 ring-emerald-50">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
                    {{ __('¡Gracias por unirte, :name!', ['name' => $name]) }}
                </h1>
                <p class="text-sm text-stone-600 mt-2 max-w-sm mx-auto font-light leading-relaxed">
                    {{ __('Tu cuenta ha sido creada exitosamente. Tu vocación de orar por los demás es un pilar fundamental para nuestra comunidad.') }}
                </p>
            </div>

            <div class="pt-4 space-y-3">
                <a href="{{ route('dashboard') }}" class="block w-full py-3 px-4 rounded-xl text-center font-semibold text-white bg-amber-600 hover:bg-amber-500 transition duration-150 shadow-md">
                    {{ __('Ir a mi Panel de Intercesión') }}
                </a>

                <a href="{{ route('home') }}" class="block w-full py-2.5 px-4 rounded-xl text-center text-sm font-medium text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition duration-150">
                    {{ __('Ir al Inicio del Portal') }}
                </a>
            </div>
        </div>
    @else
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-amber-500/10 text-amber-600 mb-3 font-bold text-lg">
                ✝
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
                {{ __('Inscripción de Intercesores') }}
            </h1>
            <p class="text-xs sm:text-sm text-stone-500 mt-1.5 font-light">
                {{ __('Únete a la comunidad de creyentes que oran por las necesidades de otros.') }}
            </p>
        </div>

        <form wire:submit="register" class="space-y-4">
            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Nombre Completo')" />
                <x-text-input wire:model="name" id="name" type="text" name="name" required autofocus autocomplete="name" :placeholder="__('Tu nombre y apellido')" />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Correo Electrónico')" />
                <x-text-input wire:model="email" id="email" type="email" name="email" required autocomplete="username" placeholder="tu@correo.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Contraseña')" />
                <x-text-input wire:model="password" id="password"
                                type="password"
                                name="password"
                                required autocomplete="new-password"
                                :placeholder="__('Mínimo 8 caracteres')" />
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" />
                <x-text-input wire:model="password_confirmation" id="password_confirmation"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password"
                                :placeholder="__('Repite tu contraseña')" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>

            <div class="pt-3 space-y-3">
                <x-primary-button>
                    {{ __('Registrarme como Intercesor') }}
                </x-primary-button>

                <div class="text-center pt-2">
                    <p class="text-xs text-stone-500">
                        {{ __('¿Ya tienes una cuenta?') }}
                        <a href="{{ route('login') }}" wire:navigate class="text-amber-600 hover:text-amber-500 font-bold ml-1">
                            {{ __('Iniciar sesión') }}
                        </a>
                    </p>
                </div>
            </div>
        </form>
    @endif
</div>
