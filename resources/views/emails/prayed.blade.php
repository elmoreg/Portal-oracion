<x-mail::message>
# ¡Buenas noticias!

Hola {{ $prayerRequest->requester_name ?: 'hermano/a' }},

Alguien en nuestro Portal de Oración ha leído tu petición y acaba de unirse en oración por ella.

> "{{ \Illuminate\Support\Str::limit($prayerRequest->content, 100) }}"

**Tu petición ha recibido apoyo en oración {{ $prayerRequest->prayed_count }} {{ $prayerRequest->prayed_count == 1 ? 'vez' : 'veces' }}.**

Recuerda que Dios está contigo.

<x-mail::button :url="route('prayer.show', $prayerRequest)">
Ver tu petición
</x-mail::button>

Gracias por confiar en nosotros,<br>
{{ config('app.name') }}
</x-mail::message>
