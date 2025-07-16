<?php

namespace App\Actions\Highway\CarrierSync;

use App\Models\Organizations\Organization;
use App\Models\Highway\HighwayCarrierSyncLog;
use App\Services\Highway\HighwayCarrierSyncClient;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class SyncCarrierProfilesFromHighwayAction
{
    use AsAction;

    public function handle(?Organization $organization = null, ?array $carrierIds = null): array
    {
        $organization = $organization ?: auth()->user()->organization;
        
        if (!$organization->hasHighwayConfigured()) {
            return [
                'success' => false,
                'message' => 'Highway not configured for organization',
                'carriers_updated' => 0,
                'errors' => ['Highway not configured']
            ];
        }

        try {
            $client = new HighwayCarrierSyncClient($organization);
            
            // Get carriers being monitored by Highway
            $monitoredCarriers = $organization->carriers()
                ->where('highway_monitored', true)
                ->when($carrierIds, function ($query, $carrierIds) {
                    return $query->whereIn('id', $carrierIds);
                })
                ->get();

            if ($monitoredCarriers->isEmpty()) {
                return [
                    'success' => true,
                    'message' => 'No carriers being monitored by Highway',
                    'carriers_updated' => 0,
                    'errors' => []
                ];
            }

            // Fetch updated profiles from Highway
            $highwayCarrierIds = $monitoredCarriers->pluck('highway_carrier_id')->filter()->toArray();
            $response = $client->syncCarrierProfiles($highwayCarrierIds);

            if (!$response['success']) {
                $this->logSyncAttempt(
                    $organization,
                    null,
                    'profile_sync',
                    'failed',
                    ['carrier_ids' => $highwayCarrierIds],
                    $response,
                    $response['error'] ?? 'Unknown error'
                );

                return [
                    'success' => false,
                    'message' => 'Failed to fetch carrier profiles from Highway',
                    'carriers_updated' => 0,
                    'errors' => [$response['error'] ?? 'Unknown error']
                ];
            }

            $updatedCount = 0;
            $errors = [];

            // Process each carrier profile from Highway
            foreach ($response['carriers'] as $highwayCarrierData) {
                try {
                    $carrier = $monitoredCarriers->firstWhere('highway_carrier_id', $highwayCarrierData['id']);
                    
                    if ($carrier) {
                        $updateResult = UpdateCarrierFromHighwayDataAction::run($carrier, $highwayCarrierData);
                        
                        if ($updateResult['success']) {
                            $updatedCount++;
                            
                            $this->logSyncAttempt(
                                $organization,
                                $carrier->id,
                                'profile_sync',
                                'success',
                                ['highway_carrier_id' => $highwayCarrierData['id']],
                                $highwayCarrierData,
                                null,
                                now()
                            );
                        } else {
                            $errors[] = "Failed to update carrier {$carrier->name}: {$updateResult['message']}";
                            
                            $this->logSyncAttempt(
                                $organization,
                                $carrier->id,
                                'profile_sync',
                                'failed',
                                ['highway_carrier_id' => $highwayCarrierData['id']],
                                $highwayCarrierData,
                                $updateResult['message']
                            );
                        }
                    }
                } catch (\Exception $e) {
                    $errors[] = "Exception updating carrier: {$e->getMessage()}";
                }
            }

            return [
                'success' => true,
                'message' => "Successfully synced {$updatedCount} carrier profiles",
                'carriers_updated' => $updatedCount,
                'total_carriers' => $monitoredCarriers->count(),
                'errors' => $errors,
                'response_time' => $response['response_time'] ?? 0
            ];

        } catch (\Exception $e) {
            $this->logSyncAttempt(
                $organization,
                null,
                'profile_sync',
                'failed',
                ['carrier_ids' => $carrierIds],
                null,
                'Exception: ' . $e->getMessage()
            );

            return [
                'success' => false,
                'message' => 'Exception during carrier profile sync',
                'carriers_updated' => 0,
                'errors' => [$e->getMessage()]
            ];
        }
    }

    public function asController(): Response
    {
        $result = $this->handle();

        if ($result['success']) {
            return response()->json([
                'message' => $result['message'],
                'data' => $result
            ]);
        }

        return response()->json([
            'message' => $result['message'],
            'errors' => $result['errors']
        ], 422);
    }

    public function authorize(): bool
    {
        return auth()->user()->can('manage carriers');
    }

    private function logSyncAttempt(
        Organization $organization,
        ?int $carrierId,
        string $syncType,
        string $status,
        ?array $requestData = null,
        ?array $responseData = null,
        ?string $errorMessage = null,
        ?\Carbon\Carbon $syncedAt = null
    ): void {
        HighwayCarrierSyncLog::create([
            'organization_id' => $organization->id,
            'carrier_id' => $carrierId,
            'sync_type' => $syncType,
            'highway_carrier_id' => $requestData['highway_carrier_id'] ?? null,
            'status' => $status,
            'request_data' => $requestData,
            'response_data' => $responseData,
            'error_message' => $errorMessage,
            'synced_at' => $syncedAt,
        ]);
    }
}