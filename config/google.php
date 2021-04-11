<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Google Maps Configuration
    |--------------------------------------------------------------------------
    |
    |
    */
    'maps' => [
        'api_key'=> env( 'GOOGLE_MAPS_API_KEY' ),
    ],
    'firebase_credentials' => env( 'GOOGLE_FIREBASE_CREDENTIALS'),
];