<?php

use App\Models\PrayerRequest;
use App\Services\GeoIp\GeoIpService;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $content = '';

    public string $requester_name = '';

    public function submit(GeoIpService $geoIp): void
    {
        $validated = $this->validate([
            'content' => ['required', 'string', 'min:5', 'max:3000'],
            'requester_name' => ['required', 'string', 'max:120'],
        ]);

        $ip = request()->ip();
        $location = $geoIp->resolve($ip);

        $prayerRequest = PrayerRequest::create([
            'content' => $validated['content'],
            'requester_name' => $validated['requester_name'],
            'ip_address' => $ip,
            'country_code' => $location?->countryCode,
            'country_name' => $location?->countryName,
        ]);

        $this->redirect(route('prayer.show', $prayerRequest), navigate: true);
    }
}; ?>

<div>
    <h1 class="text-xl font-semibold text-gray-800 mb-1">Compartí tu petición de oración</h1>
    <p class="text-sm text-gray-500 mb-6">
        No pedimos tu email ni datos de contacto: solo tu nombre para poder acompañarte. Al enviarla vas a
        recibir un enlace único y secreto para hacerle seguimiento y, si querés, conversar con quien ore por
        vos. Guardalo bien, es la única forma de volver a tu petición.
    </p>

    <form wire:submit="submit" class="space-y-4">
        <div>
            <x-input-label for="requester_name" value="Tu nombre" />
            <x-text-input wire:model="requester_name" id="requester_name" class="block mt-1 w-full" type="text" maxlength="120" required placeholder="¿Cómo te llamás?" />
            <x-input-error :messages="$errors->get('requester_name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="content" value="¿Por qué querés que oremos?" />
            <textarea
                wire:model="content"
                id="content"
                rows="6"
                required
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Contanos tu petición..."
            ></textarea>
            <x-input-error :messages="$errors->get('content')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end">
            <x-primary-button type="submit">Enviar petición</x-primary-button>
        </div>
    </form>
</div>
