<?php

namespace App\Actions\Highway\Configuration;

use App\Models\Organizations\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Lorisleiva\Actions\Concerns\AsAction;

class GetHighwayConfiguration
{
    use AsAction;

    public function handle(Organization $organization): array
    {
        $config = $organization->highwayConfiguration;

        if (!$config) {
            return [
                'api_key' => '',
                'environment' => 'staging',
                'auto_sync_enabled' => true,
                'sync_frequency' => 'daily',
                'is_configured' => false,
            ];
        }

        return [
            'api_key' => $this->maskApiKey(Crypt::decryptString($config->api_key)),
            'environment' => $config->environment,
            'auto_sync_enabled' => $config->auto_sync_enabled,
            'sync_frequency' => $config->sync_frequency,
            'is_configured' => true,
            'created_at' => $config->created_at,
            'updated_at' => $config->updated_at,
        ];
    }

    public function authorize(): bool
    {
        return auth()->user()->can('view organization settings');
    }

    private function maskApiKey(string $apiKey): string
    {
        if (strlen($apiKey) <= 8) {
            return str_repeat('•', strlen($apiKey));
        }

        return substr($apiKey, 0, 4) . str_repeat('•', strlen($apiKey) - 8) . substr($apiKey, -4);
    }
}