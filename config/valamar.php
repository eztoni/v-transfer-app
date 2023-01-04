<?php

use Illuminate\Support\Str;

return [
    'google_maps_api_key' => env('GOOGLE_MAPS_API_KEY', ''),
    'valamar_client_api_key' => env('VALAMAR_CLIENT_API_KEY',''),
    'valamar_client_api_url' => env('VALAMAR_CLIENT_API_URL',''),
    'valamar_opera_api_user' => env('VARLAMAR_OPERA_API_USER',''),
    'valamar_opera_api_pass' => env('VALAMAR_OPERA_API_PASS',''),
    'valamar_opera_api_url' => env('VALAMAR_OPERA_API_BASE_URL',''),

    'ez_dev_tools' => env('EZ_DEV_TOOLS_ENABLED',false)
];
