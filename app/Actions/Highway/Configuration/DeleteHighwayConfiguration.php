<?php

namespace App\Actions\Highway\Configuration;

use App\Models\Organizations\Organization;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteHighwayConfiguration
{
    use AsAction;

    public function handle(Organization $organization, bool $clearSyncData = false): bool
    {
        $config = $organization->highwayConfiguration;
        
        if (!$config) {
            return true; // Already deleted
        }

        // Delete the configuration
        $config->delete();

        // Optionally clear Highway-related carrier data
        if ($clearSyncData) {
            $organization->carriers()->update([
                'highway_carrier_id' => null,
                'highway_monitored' => false,
                'highway_last_synced_at' => null,
                'highway_sync_status' => 'not_synced'
            ]);

            // Clear sync logs
            $organization->highwayCarrierSyncLogs()->delete();
        }

        return true;
    }

    public function authorize(): bool
    {
        return auth()->user()->can('manage organization settings');
    }
}