<?php

// WARNING!! ********
/**
 * Anything put into this file with expose_to_frontend set to true will be exposed to the front-end of the application!!
 * 
 * expose_to_frontend is a boolean that determines if the setting should be exposed to the front-end of the application.
 * is_encrypted is a boolean that determines if the setting should be encrypted in db for overriden values
 * provider is a string that determines the provider of the setting, used for grouping on the UI mainly
 */
return [
    'google_maps.api_key' => [
        'label' => 'Google Maps API Key',
        'description' => 'The API key for the Google Maps API',
        'expose_to_frontend' => true,
        'is_encrypted' => true,
        'provider' => 'Google',
        'value' => env('GOOGLE_MAPS_API_KEY'),
    ]
];