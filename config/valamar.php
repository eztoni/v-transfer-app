<?php

use Illuminate\Support\Str;

return [
    'google_maps_api_key' => env('GOOGLE_MAPS_API_KEY', ''),
    'valamar_client_api_key' => env('VALAMAR_CLIENT_API_KEY',''),
    'valamar_client_id' => env('VALAMAR_CLIENT_ID',''),
    'valamar_client_secret' => env('VALAMAR_CLIENT_SECRET',''),
    'valamar_api_login_url' => env('VALAMAR_CLIENT_API_LOGIN_URL',''),
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

    #Fiskalizacija Valamar
    'valamar_fiskalizacija_valamar_oib' => env('VALAMAR_FISKALIZACIJA_VALAMAR_OIB',''),
    'valamar_fiskalizacija_valamar_cert' => env('VALAMAR_FISKALIZACIJA_VALAMAR_CERT',''),
    'valamar_fiskalizacija_valamar_pw' => env('VALAMAR_FISKALIZACIJA_VALAMAR_PW',''),

    #Fiskalizacija Imperial Rab
    'valamar_fiskalizacija_imperial_oib' => env('VALAMAR_FISKALIZACIJA_IMPERIAL_OIB',''),
    'valamar_fiskalizacija_imperial_cert' => env('VALAMAR_FISKALIZACIJA_IMPERIAL_CERT',''),
    'valamar_fiskalizacija_imperial_pw' => env('VALAMAR_FISKALIZACIJA_IMPERIAL_PW',''),

    'ez_dev_tools' => env('EZ_DEV_TOOLS_ENABLED',false)
];
