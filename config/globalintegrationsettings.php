<?php

// WARNING!! ********
/**
 * Anything put into this file with expose_to_frontend set to true will be exposed to the front-end of the application!!
 * 
 * expose_to_frontend is a boolean that determines if the setting should be exposed to the front-end of the application.
 * provider is a string that determines the provider of the setting, used for grouping on the UI mainly
 */
return [
    'google_maps.api_key' => [
        'label' => 'Google Maps API Key',
        'description' => 'Used for location search and map display',
        'expose_to_frontend' => true,
        'provider' => 'Google',
        'value' => env('GOOGLE_MAPS_API_KEY'),
    ],
    'highway.api_key' => [
        'label' => 'Highway API Key',
        'description' => 'API key for Highway carrier monitoring and syncing',
        'expose_to_frontend' => false,
        'provider' => 'Highway',
        'value' => env('HIGHWAY_API_KEY'),
    ],
    'highway.environment' => [
        'label' => 'Highway Environment',
        'description' => 'Highway API environment (staging or production)',
        'expose_to_frontend' => false,
        'provider' => 'Highway',
        'value' => env('HIGHWAY_ENVIRONMENT', 'staging'),
    ],
    'highway.auto_sync_enabled' => [
        'label' => 'Auto Sync Enabled',
        'description' => 'Automatically add new carriers to Highway monitoring',
        'expose_to_frontend' => false,
        'provider' => 'Highway',
        'value' => env('HIGHWAY_AUTO_SYNC_ENABLED', 'true'),
    ],
    'highway.sync_frequency' => [
        'label' => 'Sync Frequency',
        'description' => 'How often to sync carrier profiles from Highway',
        'expose_to_frontend' => false,
        'provider' => 'Highway',
        'value' => env('HIGHWAY_SYNC_FREQUENCY', 'daily'),
    ],
];