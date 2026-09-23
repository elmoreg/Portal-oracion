<?php

use App\Models\PrayerRequest;
use App\Services\GeoIp\GeoIpService;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $content = '';

    public string $requester_name = '';

    public string $email = '';

    public bool $is_public = false;

    public function submit(GeoIpService $geoIp): void
    {
        $this->validate([
            'content' => ['required', 'string', 'min:5', 'max:3000'],
            'requester_name' => ['nullable', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $ip = request()->ip();
        $location = $geoIp->resolve($ip);

        $prayerRequest = PrayerRequest::create([
            'content' => $this->content,
            'requester_name' => $this->requester_name ?: null,
            'email' => $this->email ?: null,
            'is_public' => (bool) $this->is_public,
            'ip_address' => $ip,
            'country_code' => $location?->countryCode,
            'country_name' => $location?->countryName,
        ]);

        if ($prayerRequest->email) {
            \Illuminate\Support\Facades\Mail::to($prayerRequest->email)->send(new \App\Mail\PrayerRequestCreated($prayerRequest));
        }

        session()->flash('petition_created', true);

        $this->redirect(route('prayer.show', $prayerRequest));
    }
}; ?>

<div>
    <!-- Form Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-amber-500/10 text-amber-600 mb-4 shadow-inner">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
            </svg>
        </div>

        <div class="inline-flex items-center justify-center gap-2 mb-2">
            <span class="h-px w-6 bg-amber-500"></span>
            <span class="text-amber-600 font-bold tracking-[0.2em] uppercase text-[10px]">{{ __('Confidencial & Seguro') }}</span>
            <span class="h-px w-6 bg-amber-500"></span>
        </div>

        <h1 class="font-serif text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
            {{ __('Comparte tu Petición de Oración') }}
        </h1>
        
        <p class="text-slate-600 text-xs sm:text-sm mt-2 leading-relaxed font-light">
            {{ __('Al enviar tu petición recibirás un enlace privado único para hacerle seguimiento y conversar con los intercesores asignados.') }}
        </p>
    </div>

    <!-- The Form -->
    <form wire:submit="submit" class="space-y-5 text-start">
        <div>
            <x-input-label for="requester_name" :value="__('Tu Nombre (Opcional)')" />
            <x-text-input wire:model="requester_name" id="requester_name" type="text" maxlength="120" :placeholder="__('Puedes dejarlo en blanco si prefieres ser anónimo')" />
            <x-input-error :messages="$errors->get('requester_name')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Correo Electrónico (Opcional)')" />
            <x-text-input wire:model="email" id="email" type="email" maxlength="255" placeholder="ejemplo@correo.com" />
            <p class="mt-1 text-[11px] text-stone-500">{{ __('Solo lo usaremos para enviarte el enlace y avisarte cuando alguien ore por ti.') }}</p>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div>
            <div class="flex justify-between items-center mb-1">
                <x-input-label for="content" :value="__('¿Por qué necesidad quieres que oremos?')" />
                <span class="text-[11px] text-stone-400 font-medium" x-data="{ count: 0 }" x-init="$watch('$wire.content', value => count = value ? value.length : 0)" x-text="count + ' / 3000'"></span>
            </div>
            <textarea
                wire:model="content"
                id="content"
                rows="5"
                required
                class="block w-full rounded border-stone-300 bg-stone-50 text-slate-900 shadow-sm focus:border-amber-500 focus:ring-amber-500 focus:bg-white transition-all text-sm resize-none p-3.5 placeholder-stone-400"
                placeholder="{{ __('Escribe aquí con total libertad y confianza tu motivo de oración...') }}"
            ></textarea>
            <x-input-error :messages="$errors->get('content')" class="mt-1" />
        </div>

        <!-- Checkbox Muro Público -->
        <div class="rounded-xl bg-stone-50 border border-stone-200 p-4">
            <div class="flex items-start gap-3">
                <div class="flex items-center h-5 mt-0.5">
                    <input wire:model="is_public" id="is_public" type="checkbox" class="w-4 h-4 text-amber-500 bg-white border-stone-300 rounded focus:ring-amber-500 focus:ring-2">
                </div>
                <div class="text-xs">
                    <label for="is_public" class="font-bold text-slate-900 cursor-pointer">
                        {{ __('Publicar también en el Muro Comunitario') }}
                    </label>
                    <p class="text-stone-500 mt-0.5 leading-relaxed font-light">
                        {{ __('Permite que más creyentes de la comunidad lean tu petición y oren por ti. (Seguirá siendo anónima si no colocaste tu nombre).') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" 
                    wire:loading.attr="disabled"
                    class="w-full inline-flex justify-center items-center px-6 py-4 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-widest rounded shadow-md hover:shadow-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2" 
                    wire:loading.class="opacity-75 cursor-not-allowed">
                <span wire:loading.remove wire:target="submit" class="inline-flex items-center gap-2">
                    <span>{{ __('Enviar Petición de Oración') }}</span>
                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </span>
                <span wire:loading wire:target="submit" class="inline-flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-slate-950" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span>{{ __('Enviando petición...') }}</span>
                </span>
            </button>
        </div>
    </form>
</div>
