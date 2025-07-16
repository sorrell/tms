<?php

namespace App\Services\Highway;

use App\Models\Organizations\Organization;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HighwayCarrierSyncClient
{
    private Organization $organization;
    private string $baseUrl;
    private string $apiKey;

    public function __construct(Organization $organization)
    {
        $this->organization = $organization;
        
        $config = $organization->highwayConfiguration;
        if (!$config) {
            throw new \Exception('Highway configuration not found for organization');
        }

        $this->apiKey = decrypt($config->api_key);
        $this->baseUrl = $config->environment === 'production'
            ? 'https://highway.com/core/connect/external_api/v1'
            : 'https://staging.highway.com/core/connect/external_api/v1';
    }

    /**
     * Add carrier to Highway monitoring
     */
    public function addCarrierToMonitoring(array $carrierData): array
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl . '/carriers/monitor', $carrierData);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Highway: Carrier added to monitoring', [
                    'organization_id' => $this->organization->id,
                    'carrier_data' => $carrierData,
                    'highway_response' => $data
                ]);

                return [
                    'success' => true,
                    'highway_carrier_id' => $data['id'] ?? null,
                    'data' => $data,
                    'response_time' => $response->handlerStats()['total_time'] ?? 0
                ];
            }

            Log::warning('Highway: Failed to add carrier to monitoring', [
                'organization_id' => $this->organization->id,
                'status_code' => $response->status(),
                'response_body' => $response->body(),
                'carrier_data' => $carrierData
            ]);

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Unknown error',
                'status_code' => $response->status(),
                'response_body' => $response->body()
            ];

        } catch (RequestException $e) {
            Log::error('Highway: Request exception adding carrier to monitoring', [
                'organization_id' => $this->organization->id,
                'error' => $e->getMessage(),
                'carrier_data' => $carrierData
            ]);

            return [
                'success' => false,
                'error' => 'Network error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get updated carrier profiles from Highway
     */
    public function syncCarrierProfiles(array $carrierIds = []): array
    {
        try {
            $params = [];
            if (!empty($carrierIds)) {
                $params['carrier_ids'] = implode(',', $carrierIds);
            }

            $response = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get($this->baseUrl . '/carriers/profiles', $params);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Highway: Carrier profiles synced', [
                    'organization_id' => $this->organization->id,
                    'carrier_count' => count($data['data'] ?? []),
                    'carrier_ids_filter' => $carrierIds
                ]);

                return [
                    'success' => true,
                    'carriers' => $data['data'] ?? [],
                    'pagination' => $data['pagination'] ?? null,
                    'response_time' => $response->handlerStats()['total_time'] ?? 0
                ];
            }

            Log::warning('Highway: Failed to sync carrier profiles', [
                'organization_id' => $this->organization->id,
                'status_code' => $response->status(),
                'response_body' => $response->body()
            ]);

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Unknown error',
                'status_code' => $response->status()
            ];

        } catch (RequestException $e) {
            Log::error('Highway: Request exception syncing carrier profiles', [
                'organization_id' => $this->organization->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Network error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check Highway monitoring status for a carrier
     */
    public function getCarrierMonitoringStatus(string $highwayCarrierId): array
    {
        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get($this->baseUrl . '/carriers/' . $highwayCarrierId . '/status');

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'status' => $data,
                    'response_time' => $response->handlerStats()['total_time'] ?? 0
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Unknown error',
                'status_code' => $response->status()
            ];

        } catch (RequestException $e) {
            Log::error('Highway: Request exception checking carrier status', [
                'organization_id' => $this->organization->id,
                'highway_carrier_id' => $highwayCarrierId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Network error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Test API connection
     */
    public function testConnection(): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get($this->baseUrl . '/carriers/profiles', ['limit' => 1]);

            return [
                'success' => $response->successful(),
                'status_code' => $response->status(),
                'response_time' => $response->handlerStats()['total_time'] ?? 0,
                'error' => $response->successful() ? null : $response->body()
            ];

        } catch (RequestException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}