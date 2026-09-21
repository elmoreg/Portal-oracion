<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Administrador inicial
    |--------------------------------------------------------------------------
    |
    | Usado por el seeder para crear (o localizar) la primera cuenta de
    | administrador del portal. Cambiá estos valores en tu .env antes de
    | desplegar a producción, y cambiá la contraseña apenas inicies sesión.
    |
    */

    'admin_email' => env('PORTAL_ADMIN_EMAIL', 'admin@portal-oracion.test'),

    'admin_password' => env('PORTAL_ADMIN_PASSWORD', 'password'),

];
