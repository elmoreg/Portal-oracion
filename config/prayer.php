<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Asignación automática de peticiones de oración
    |--------------------------------------------------------------------------
    |
    | Parámetros que controlan cómo el scheduler distribuye las peticiones
    | pendientes entre los intercesores activos cada hora.
    |
    */

    'assignment' => [

        /*
         | Número máximo de peticiones activas (status "assigned" o "praying")
         | que puede tener un intercesor en simultáneo.
         */
        'max_per_intercessor' => (int) env('PRAYER_MAX_PER_INTERCESSOR', 10),

        /*
         | Máximo de peticiones a procesar en un solo ciclo de asignación,
         | para no bloquear la base de datos en un lote muy grande.
         */
        'batch_size' => (int) env('PRAYER_ASSIGNMENT_BATCH_SIZE', 100),

    ],

];
