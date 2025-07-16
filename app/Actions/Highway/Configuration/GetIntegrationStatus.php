<?php

namespace App\Actions\Highway\Configuration;

use App\Models\Organizations\Organization;
use Lorisleiva\Actions\Concerns\AsAction;

class GetIntegrationStatus
{
    use AsAction;

    public function handle(Organization $organization): array
    {
        $config = $organization->highwayConfiguration;
        
        if (!$config) {
            return [
                'configured' => false,
                'status' => 'not_configured',
                'carriers_monitored' => 0,
                'last_sync' => null,
                'sync_errors' => 0,
            ];
        }

        $carriersMonitored = $organization->carriers()
            ->where('highway_monitored', true)
            ->count();

        $recentSyncLogs = $organization->highwayCarrierSyncLogs()
            ->where('created_at', '>=', now()->subDays(7))
            ->get();

        $syncErrors = $recentSyncLogs
            ->where('status', 'failed')
            ->count();

        $lastSync = $organization->highwayCarrierSyncLogs()
            ->where('status', 'success')
            ->latest('synced_at')
            ->first();

        // Determine overall status
        $status = 'healthy';
        if ($syncErrors > 0) {
            $status = 'errors';
        } elseif (!$lastSync || $lastSync->synced_at->lt(now()->subDays(2))) {
            $status = 'stale';
        }

        return [
            'configured' => true,
            'status' => $status,
            'environment' => $config->environment,
            'auto_sync_enabled' => $config->auto_sync_enabled,
            'carriers_monitored' => $carriersMonitored,
            'last_sync' => $lastSync?->synced_at,
            'sync_errors' => $syncErrors,
            'total_sync_attempts' => $recentSyncLogs->count(),
        ];
    }

    public function authorize(): bool
    {
        return auth()->user()->can('view organization settings');
    }
}