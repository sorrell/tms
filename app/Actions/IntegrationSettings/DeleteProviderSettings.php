<?php

namespace App\Actions\IntegrationSettings;

use Exception;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteProviderSettings
{
    use AsAction;

    /**
     * Delete all settings for a provider
     */
    public function handle(string $provider)
    {
        $organization = current_organization();

        if (!$organization) {
            throw new Exception("A current organization is required to delete integration settings");
        }

        $settings = $organization->integrationSettings()
            ->where('provider', $provider)
            ->get();
            
        $result = $organization->integrationSettings()
            ->where('provider', $provider)
            ->delete();
            
        // Clear all relevant caches
        foreach ($settings as $setting) {
            ClearIntegrationCache::run($setting->key, $setting->provider);
        }
        
        return $result > 0;
    }
}