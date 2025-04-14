<?php

namespace App\Actions\IntegrationSettings;

use Exception;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class SetIntegrationSetting
{
    use AsAction;

    /**
     * Set an integration setting value
     */
    public function handle(string $key, $value, ?string $provider = null, bool $encrypt = false, bool $exposeToFrontend = false)
    {

        $organization = current_organization();

        if (!$organization) {
            throw new Exception("A current organization is required to set integration settings");
        }

        $organization->integrationSettings()->updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'provider' => $provider,
                'is_encrypted' => $encrypt,
                'expose_to_frontend' => $exposeToFrontend,
            ]
        );
        
        return true;
    }
}