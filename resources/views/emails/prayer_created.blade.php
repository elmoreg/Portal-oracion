<x-mail::message>
# Hola {{ $prayerRequest->requester_name ?: 'hermano/a' }},

Hemos recibido tu petición de oración. Este correo contiene el enlace único y privado para que puedas hacerle seguimiento. 

> "{{ $prayerRequest->content }}"

**Guarda este correo.** El enlace de abajo es la única forma que tienes para volver a ver tu petición, comprobar si alguien ha orado por ella, o comunicarte con el intercesor asignado.

<x-mail::button :url="route('prayer.show', $prayerRequest)">
Ver mi petición
</x-mail::button>

Que Dios te bendiga,<br>
{{ config('app.name') }}
</x-mail::message>
