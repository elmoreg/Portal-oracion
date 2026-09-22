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

    public function submit(GeoIpService $geoIp): void
    {
        $validated = $this->validate([
            'content' => ['required', 'string', 'min:5', 'max:3000'],
            'requester_name' => ['nullable', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $ip = request()->ip();
        $location = $geoIp->resolve($ip);

        $prayerRequest = PrayerRequest::create([
            'content' => $validated['content'],
            'requester_name' => $validated['requester_name'] ?: null,
            'email' => $validated['email'] ?: null,
            'ip_address' => $ip,
            'country_code' => $location?->countryCode,
            'country_name' => $location?->countryName,
        ]);

        $this->redirect(route('prayer.show', $prayerRequest), navigate: true);
    }
}; ?>

<div class="text-center px-4">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-soul-indigo/5 text-soul-gold mb-6">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
    </div>

    <h1 class="text-3xl font-serif text-soul-indigo mb-3">Comparte tu petición de oración</h1>
    <p class="text-soul-accent mb-8 leading-relaxed">
        Tu petición es anónima. Al enviarla recibirás un enlace único y secreto para hacerle seguimiento. 
        <strong class="font-medium text-soul-indigo/70">Guárdalo bien</strong>, es la única forma de volver a tu petición.
    </p>

    <form wire:submit="submit" class="space-y-6 text-left">
        <div>
            <x-input-label for="requester_name" value="Tu nombre (opcional)" class="text-soul-indigo" />
            <x-text-input wire:model="requester_name" id="requester_name" class="block mt-1 w-full bg-gray-50 border-gray-200 focus:border-soul-gold focus:ring-soul-gold transition-colors" type="text" maxlength="120" placeholder="Puedes dejarlo en blanco" />
            <x-input-error :messages="$errors->get('requester_name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="Tu correo electrónico (opcional)" class="text-soul-indigo" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full bg-gray-50 border-gray-200 focus:border-soul-gold focus:ring-soul-gold transition-colors" type="email" maxlength="255" placeholder="Para enviarte el seguimiento de tu petición" />
            <p class="mt-1 text-sm text-soul-accent/80">Solo lo usaremos para enviarte actualizaciones sobre tu petición de oración.</p>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="flex justify-between items-center mb-1">
                <x-input-label for="content" value="¿Por qué quieres que oremos?" class="text-soul-indigo" />
                <span class="text-xs text-soul-accent" x-data="{ count: 0 }" x-init="$watch('$wire.content', value => count = value.length)" x-text="count + ' / 3000'"></span>
            </div>
            <textarea
                wire:model="content"
                id="content"
                rows="6"
                required
                class="block mt-1 w-full rounded-xl border-gray-200 bg-gray-50 shadow-sm focus:border-soul-gold focus:ring-soul-gold transition-colors resize-none"
                placeholder="Cuéntanos tu petición..."
            ></textarea>
            <x-input-error :messages="$errors->get('content')" class="mt-2" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full flex justify-center items-center px-6 py-3.5 border border-transparent rounded-full shadow-sm text-base font-medium text-white bg-soul-indigo hover:bg-soul-indigo/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-soul-gold transition-all" wire:loading.class="opacity-75 cursor-not-allowed">
                <span wire:loading.remove wire:target="submit">Enviar petición</span>
                <span wire:loading wire:target="submit" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Enviando...
                </span>
            </button>
        </div>
    </form>
</div>
