<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Base de datos de geolocalización por IP
    |--------------------------------------------------------------------------
    |
    | Resolvemos país (y, si está disponible, ciudad/región) de forma local,
    | sin enviar la IP del solicitante a ningún servicio externo. Por defecto
    | usamos la base de datos gratuita de país (DB-IP Lite, CC BY 4.0) que se
    | distribuye con la aplicación.
    |
    | Si necesitás mayor precisión (ciudad/región), podés colocar una base de
    | datos .mmdb con esos campos (por ejemplo DB-IP City Lite o MaxMind
    | GeoLite2 City) en la ruta indicada por GEOIP_CITY_DATABASE_PATH; si el
    | archivo existe, se usa automáticamente en lugar de la de país.
    |
    */

    'country_database_path' => env('GEOIP_COUNTRY_DATABASE_PATH', resource_path('geoip/dbip-country.mmdb')),

    'city_database_path' => env('GEOIP_CITY_DATABASE_PATH', storage_path('app/private/geoip/city.mmdb')),

    'countries_es_path' => resource_path('geoip/countries_es.php'),

    /*
    |--------------------------------------------------------------------------
    | Atribución
    |--------------------------------------------------------------------------
    |
    | La base de datos DB-IP Lite se distribuye bajo licencia CC BY 4.0, que
    | exige un enlace de atribución visible en las páginas que muestren datos
    | derivados de ella. No lo quites si seguís usando la base por defecto.
    |
    */
    'attribution_html' => 'Geolocalización por IP: <a href="https://db-ip.com" target="_blank" rel="noopener" class="underline">DB-IP.com</a>',
];
