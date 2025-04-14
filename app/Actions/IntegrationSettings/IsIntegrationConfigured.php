<?php

namespace App\Actions\IntegrationSettings;

use Exception;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class IsIntegrationConfigured
{
    use AsAction;

    protected int $cacheDuration = 3600;

    static function getCacheKey(string $provider) : string 
    {
        $organization = current_organization();
        if (!$organization) {
            throw new Exception("A current organization is required to check integration configuration");
        }

        return "org_{$organization->id}_integration_provider_{$provider}_configured";
    }

    /**
     * Check if an integration is configured for a provider
     */
    public function handle(string $provider): bool
    {
        $organization = current_organization();
        if (!$organization) {
            throw new Exception("A current organization is required to check integration configuration");
        }

        $cacheKey = self::getCacheKey($provider);
        
        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($organization, $provider) {
            return $organization->integration_settings()
                ->where('provider', $provider)
                ->exists();
        });
    }
} 