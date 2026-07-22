<?php

return [
    'CUIT' => env('AFIP_CUIT'),
    'cert' => storage_path('app/afip/certificado.crt'),
    'key'  => storage_path('app/afip/clave.key'),
    'production' => env('AFIP_PROD', false),
];
