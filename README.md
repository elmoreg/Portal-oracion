# Portal de Oración

Portal para recibir peticiones de oración de forma anónima, distribuirlas entre
personas inscritas para orar, y darles seguimiento hasta que sean contestadas.

## Cómo funciona

- **Petición anónima con enlace único.** Cualquiera puede enviar una petición
  sin crear cuenta ni dar datos de contacto. Al enviarla recibe un enlace
  privado (`/p/{token}`) que es la única forma de volver a ver el estado de su
  petición y de conversar con quien ora por ella. El token es un string
  aleatorio de 40 caracteres — no es adivinable ni enumerable.
- **Intercesores inscritos.** Cualquiera puede registrarse (`/register`) como
  intercesor: alguien dispuesto a orar. El equipo de administración les asigna
  peticiones desde el panel de administración.
- **Panel de administración** (`/admin`, requiere rol `admin`): ve todas las
  peticiones entrantes, filtra por estado y por zona geográfica, asigna
  intercesores y gestiona quién está inscrito.
- **Zona geográfica por IP.** Cada petición guarda el país que corresponde a
  la IP de quien la envió, resuelto 100% offline (sin enviar la IP a ningún
  servicio externo) usando una base de datos local. Esto permite agrupar y
  orar por zonas.
- **Chat privado por petición.** En la misma URL única de la petición hay una
  conversación (con actualización automática) entre quien pidió oración y
  quien ora por ella — útil para consejería o acompañamiento más personal.

## Stack

Laravel 13 + Livewire/Volt (Breeze) + Tailwind CSS + SQLite (por defecto).

## Puesta en marcha

```bash
composer install
npm install && npm run build   # o `npm run dev` durante desarrollo

cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate --seed
```

El seeder crea una cuenta de administrador inicial usando `PORTAL_ADMIN_EMAIL`
/ `PORTAL_ADMIN_PASSWORD` del `.env` (por defecto
`admin@portal-oracion.test` / `password`). **Cambiá esa contraseña** apenas
inicies sesión, o definí esas variables antes de sembrar la base en
producción.

Para convertir a cualquier otra cuenta ya registrada en administradora:

```bash
php artisan portal:make-admin correo@ejemplo.com
```

Levantar el servidor de desarrollo:

```bash
php artisan serve
```

## Geolocalización por IP (offline)

Por defecto se usa `resources/geoip/dbip-country.mmdb`, una base de datos
gratuita de país-por-IP ([DB-IP Lite](https://db-ip.com/), licencia
CC BY 4.0) que se distribuye junto con la aplicación — no requiere cuenta,
API key ni conexión a internet en tiempo de ejecución. Por eso el panel de
administración muestra el enlace de atribución a DB-IP.com junto a la zona
geográfica, como exige la licencia.

Si necesitás mayor precisión (ciudad/región en lugar de solo país), colocá un
archivo `.mmdb` con ese detalle (por ejemplo DB-IP City Lite o MaxMind
GeoLite2 City) en la ruta que indique `GEOIP_CITY_DATABASE_PATH` (por
defecto `storage/app/private/geoip/city.mmdb`) — si el archivo existe, se usa
automáticamente en lugar de la base de país. Ver `config/geoip.php`.

## Roles

- **admin**: gestiona el panel de administración. Se crea por seeder o con
  `php artisan portal:make-admin`.
- **intercessor**: rol por defecto de cualquier cuenta que se registra desde
  `/register`. Ve únicamente las peticiones que el admin le asignó.

Quien envía una petición **no tiene cuenta**: su identidad es el enlace
único que recibe.

## Tests

```bash
php artisan test
```

Cubre el flujo completo: creación anónima de peticiones con token único,
acceso restringido por token, marcar una petición como contestada,
asignación de intercesores por un admin, aislamiento de las peticiones que
ve cada intercesor, y control de acceso por rol.

## Estructura relevante

```
app/Enums/                     Estados y roles (PrayerRequestStatus, UserRole, MessageAuthorType)
app/Models/PrayerRequest.php   Petición de oración (token público, estado, geo)
app/Models/PrayerMessage.php   Mensajes del chat de una petición
app/Services/GeoIp/            Resolución de zona geográfica por IP, offline
app/Livewire/PrayerChat.php    Chat reutilizable (polling) embebido en las 3 vistas
resources/views/livewire/pages/
  public/                      Crear petición y verla por su token único
  intercessor/                 Panel del intercesor (peticiones asignadas + chat)
  admin/                       Panel de administración (listado, asignación, intercesores)
```
