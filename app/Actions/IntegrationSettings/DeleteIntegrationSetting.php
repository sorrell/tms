<?php

namespace App\Actions\IntegrationSettings;

use Exception;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteIntegrationSetting
{
    use AsAction;

    /**
     * Delete an integration setting by key
     */
    public function handle(string $key)
    {
        $organization = current_organization();

        if (!$organization) {
            throw new Exception("A current organization is required to delete integration settings");
        }

        $setting = $organization->integrationSettings()
            ->where('key', $key)
            ->first();
        
        $result = $setting->delete();
        
        // Clear the cache for this key
        ClearIntegrationCache::run($key, $setting->provider);
        
        return $result > 0;
    }
} 