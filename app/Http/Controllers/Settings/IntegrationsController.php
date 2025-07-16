<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Organizations\Organization;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IntegrationsController extends Controller
{
    public function index(Organization $organization)
    {
        $this->authorize('view', $organization);

        $integrations = [
            [
                'name' => 'Highway',
                'slug' => 'highway',
                'description' => 'Carrier monitoring and compliance data synchronization',
                'status' => $organization->hasHighwayConfigured() ? 'configured' : 'not_configured',
                'configureUrl' => route('settings.integrations.highway.index', $organization),
                'details' => $organization->hasHighwayConfigured() 
                    ? 'Monitoring ' . $organization->carriers()->where('highway_monitored', true)->count() . ' carriers'
                    : 'Connect to Highway for automated carrier monitoring',
            ],
            // Future integrations can be added here
        ];

        return Inertia::render('Settings/Integrations/Index', [
            'organization' => $organization,
            'integrations' => $integrations,
        ]);
    }
}