<?php

use Illuminate\Support\Str;

return [
    'google_maps_api_key' => env('GOOGLE_MAPS_API_KEY', ''),
    'valamar_client_api_key' => env('VALAMAR_CLIENT_API_KEY',''),
    'valamar_client_api_url' => env('VALAMAR_CLIENT_API_URL',''),
    'valamar_opera_api_user' => env('VARLAMAR_OPERA_API_USER',''),
    'valamar_opera_api_pass' => env('VALAMAR_OPERA_API_PASS',''),
    'valamar_opera_api_url' => env('VALAMAR_OPERA_API_BASE_URL',''),

    #Fiskalizacija Demo Mode
    'valamar_fiskalizacija_demo_mode' => env('VALAMAR_FISKALIZACIJA_DEMO_MODE',''),
    'valamar_fiskalizacija_demo_oib' => env('VALAMAR_FISKALIZACIJA_DEMO_OIB',''),
    'valamar_fiskalizacija_demo_cert' => env('VALAMAR_FISKALIZACIJA_DEMO_CERT',''),
    'valamar_fiskalizacija_demo_cert_pw' => env('VALAMAR_FISKALIZACIJA_DEMO_CERT_PW',''),
    'valamar_opera_fiskalizacija_active' => env('VALAMAR_FISKALIZACIJA_OPERA_ACTIVE',''),


    'ez_dev_tools' => env('EZ_DEV_TOOLS_ENABLED',false)
];
