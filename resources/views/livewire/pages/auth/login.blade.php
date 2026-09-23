<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirect(route('dashboard', absolute: false));
    }
}; ?>

<div>
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-amber-500/10 text-amber-600 mb-3 shadow-inner">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 3v18M6 9h12"></path></svg>
        </div>
        <h1 class="font-serif text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
            {{ __('Ingreso al Panel') }}
        </h1>
        <p class="text-xs sm:text-sm text-stone-500 mt-1.5 font-light">
            {{ __('Acceso para intercesores y administradores del portal.') }}
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-5">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Correo Electrónico')" />
            <x-text-input wire:model="form.email" id="email" type="email" name="email" required autofocus autocomplete="username" placeholder="tu@correo.com" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1">
                <x-input-label for="password" :value="__('Contraseña')" />
                @if (Route::has('password.request'))
                    <a class="text-xs text-amber-600 hover:text-amber-500 transition-colors font-medium" href="{{ route('password.request') }}" wire:navigate>
                        {{ __('¿Olvidaste tu contraseña?') }}
                    </a>
                @endif
            </div>

            <x-text-input wire:model="form.password" id="password"
                            type="password"
                            name="password"
                            required autocomplete="current-password" 
                            placeholder="••••••••" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-1" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-stone-300 text-amber-500 shadow-sm focus:ring-amber-500 w-4 h-4" name="remember">
            <label for="remember" class="ms-2 text-xs text-stone-600 cursor-pointer">{{ __('Recordar mi sesión') }}</label>
        </div>

        <div class="pt-2 space-y-3">
            <x-primary-button>
                {{ __('Iniciar Sesión') }}
            </x-primary-button>

            <div class="text-center pt-2">
                <p class="text-xs text-stone-500">
                    {{ __('¿Quieres ser intercesor?') }}
                    <a href="{{ route('register') }}" wire:navigate class="text-amber-600 hover:text-amber-500 font-bold ml-1">
                        {{ __('Inscríbete aquí') }}
                    </a>
                </p>
            </div>
        </div>
    </form>
</div>
