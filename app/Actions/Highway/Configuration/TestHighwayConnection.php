<?php

namespace App\Actions\Highway\Configuration;

use App\Models\Organizations\Organization;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;

class TestHighwayConnection
{
    use AsAction;

    public function handle(Organization $organization, ?string $apiKey = null): array
    {
        $config = $organization->highwayConfiguration;
        
        if (!$config && !$apiKey) {
            return [
                'success' => false,
                'message' => 'No Highway configuration found',
                'error' => 'Configuration not found'
            ];
        }

        $testApiKey = $apiKey ?: decrypt($config->api_key);
        $environment = $config->environment ?? 'staging';
        $baseUrl = $environment === 'production' 
            ? 'https://highway.com/core/connect/external_api/v1'
            : 'https://staging.highway.com/core/connect/external_api/v1';

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $testApiKey,
                    'Accept' => 'application/json',
                ])
                ->get($baseUrl . '/carriers/profiles', [
                    'limit' => 1
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Connection successful',
                    'response_time' => $response->handlerStats()['total_time'] ?? 0
                ];
            }

            return [
                'success' => false,
                'message' => 'Connection failed',
                'error' => $response->body(),
                'status_code' => $response->status()
            ];

        } catch (RequestException $e) {
            return [
                'success' => false,
                'message' => 'Connection timeout or network error',
                'error' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Unexpected error occurred',
                'error' => $e->getMessage()
            ];
        }
    }

    public function authorize(): bool
    {
        return auth()->user()->can('manage organization settings');
    }
}