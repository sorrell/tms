<?php

namespace App\Actions\IntegrationSettings;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * This will get frontend integration settings plus any fallback defaults
 */
class GetFrontendIntegrationSettings
{
    use AsAction;

    protected int $cacheDuration = 3600;

    static function getCacheKey() : string 
    {
        $organization = current_organization();
        if (!$organization) {
            throw new Exception("A current organization is required to get frontend integration settings");
        }

        return "org_{$organization->id}_frontend_integration_settings";
    }

    public function handle() : Collection
    {
        $organization = current_organization();
        if (!$organization) {
            throw new Exception("A current organization is required to get frontend integration settings");
        }

        $cacheKey = self::getCacheKey();
        
        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($organization) {

            // Default settings
            $settings = config('integrationsettings');

            // Get all organization settings that are exposed to frontend
            $orgSettings = $organization->integration_settings()
                ->where('expose_to_frontend', true)
                ->get()
                ->mapWithKeys(function ($setting) {
                    return [$setting->key => $setting->value];
                })
                ->toArray();

            
            // Merge default settings with organization settings
            // Organization settings will override defaults with the same keys
            return collect(array_merge($settings, $orgSettings));
        });
    }
}