<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div>
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-amber-500/10 text-amber-600 mb-3 shadow-inner">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 3v18M6 9h12"></path></svg>
        </div>
        <h1 class="font-serif text-2xl font-bold text-slate-900 tracking-tight">
            {{ __('Recuperar Contraseña') }}
        </h1>
        <p class="text-xs text-stone-500 mt-1.5 font-light">
            {{ __('Indícanos tu correo electrónico y te enviaremos un enlace seguro para restablecer tu contraseña.') }}
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="space-y-4">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Correo Electrónico')" />
            <x-text-input wire:model="email" id="email" type="email" name="email" required autofocus placeholder="tu@correo.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div class="pt-2 space-y-3">
            <x-primary-button>
                {{ __('Enviar Enlace de Recuperación') }}
            </x-primary-button>

            <div class="text-center pt-1">
                <a href="{{ route('login') }}" wire:navigate class="text-xs text-amber-600 hover:text-amber-500 font-bold">
                    &larr; {{ __('Volver a Iniciar Sesión') }}
                </a>
            </div>
        </div>
    </form>
</div>
