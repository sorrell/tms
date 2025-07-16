<?php

namespace App\Http\Controllers\Settings;

use App\Actions\Highway\Configuration\GetHighwayConfiguration;
use App\Actions\Highway\Configuration\GetIntegrationStatus;
use App\Actions\Highway\Configuration\TestHighwayConnection;
use App\Actions\Highway\Configuration\UpdateHighwayConfiguration;
use App\Http\Controllers\Controller;
use App\Models\Organizations\Organization;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HighwayIntegrationController extends Controller
{
    public function index(Organization $organization)
    {
        $this->authorize('view', $organization);

        $configuration = GetHighwayConfiguration::run($organization);
        $integrationStatus = GetIntegrationStatus::run($organization);

        return Inertia::render('Settings/Integrations/Highway/Configuration', [
            'organization' => $organization,
            'configuration' => $configuration,
            'integrationStatus' => $integrationStatus,
        ]);
    }

    public function store(Request $request, Organization $organization)
    {
        $this->authorize('update', $organization);

        $validated = $request->validate([
            'api_key' => 'required|string|min:20',
            'environment' => 'required|in:staging,production',
            'auto_sync_enabled' => 'boolean',
            'sync_frequency' => 'string|in:daily,weekly',
        ]);

        UpdateHighwayConfiguration::run($organization, $validated);

        return back()->with('success', 'Highway configuration updated successfully.');
    }

    public function test(Request $request, Organization $organization)
    {
        $this->authorize('update', $organization);

        $request->validate([
            'api_key' => 'sometimes|string',
            'environment' => 'sometimes|in:staging,production',
        ]);

        $result = TestHighwayConnection::run(
            $organization,
            $request->input('api_key')
        );

        return response()->json($result);
    }
}