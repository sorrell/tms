<?php

namespace App\Actions\Highway\CarrierSync;

use App\Models\Carriers\Carrier;
use App\Models\Highway\HighwayCarrierSyncLog;
use App\Services\Highway\HighwayCarrierSyncClient;
use Illuminate\Http\Request;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class AddCarrierToHighwayMonitoringAction
{
    use AsAction;

    public function handle(Carrier $carrier): bool
    {
        // Validate carrier has required data
        $validation = ValidateCarrierForHighwaySyncAction::run($carrier);
        if (!$validation['valid']) {
            $this->logSyncAttempt($carrier, 'add_to_monitoring', 'failed', null, null, $validation['message']);
            return false;
        }

        // Check if already being monitored
        if ($carrier->highway_monitored) {
            return true; // Already monitored
        }

        $organization = $carrier->organization;
        if (!$organization->hasHighwayConfigured()) {
            $this->logSyncAttempt($carrier, 'add_to_monitoring', 'failed', null, null, 'Highway not configured for organization');
            return false;
        }

        try {
            $client = new HighwayCarrierSyncClient($organization);
            
            // Prepare carrier data for Highway
            $carrierData = $this->prepareCarrierData($carrier);
            
            // Send to Highway API
            $response = $client->addCarrierToMonitoring($carrierData);
            
            if ($response['success']) {
                // Update carrier record with Highway ID
                $carrier->update([
                    'highway_carrier_id' => $response['highway_carrier_id'],
                    'highway_monitored' => true,
                    'highway_sync_status' => 'monitoring',
                    'highway_last_synced_at' => now(),
                ]);

                $this->logSyncAttempt(
                    $carrier, 
                    'add_to_monitoring', 
                    'success', 
                    $carrierData, 
                    $response['data'],
                    null,
                    now()
                );

                return true;
            }

            // Log failure
            $this->logSyncAttempt(
                $carrier, 
                'add_to_monitoring', 
                'failed', 
                $carrierData, 
                $response,
                $response['error'] ?? 'Unknown error'
            );

            $carrier->update(['highway_sync_status' => 'sync_failed']);
            return false;

        } catch (\Exception $e) {
            $this->logSyncAttempt(
                $carrier, 
                'add_to_monitoring', 
                'failed', 
                null, 
                null,
                'Exception: ' . $e->getMessage()
            );

            $carrier->update(['highway_sync_status' => 'sync_failed']);
            return false;
        }
    }

    public function asController(ActionRequest $request)
    {
        $carrier = Carrier::findOrFail($request->route('carrier'));
        
        $success = $this->handle($carrier);

        if ($success) {
            return back()->with('success', 'Carrier successfully added to Highway monitoring.');
        }

        return back()->with('error', 'Failed to add carrier to Highway monitoring. Check sync logs for details.');
    }

    public function rules(): array
    {
        return [];
    }

    public function authorize(): bool
    {
        return auth()->user()->can('manage carriers');
    }

    private function prepareCarrierData(Carrier $carrier): array
    {
        return [
            'dot_number' => $carrier->dot_number,
            'company_name' => $carrier->name,
            'dba_name' => $carrier->dba_name,
            'address' => [
                'street' => $carrier->address,
                'city' => $carrier->city,
                'state' => $carrier->state,
                'zip' => $carrier->zip_code,
            ],
            'phone' => $carrier->phone,
            'email' => $carrier->email,
            'mc_number' => $carrier->mc_number,
            'external_id' => (string) $carrier->id, // LoadPartner carrier ID
        ];
    }

    private function logSyncAttempt(
        Carrier $carrier, 
        string $syncType, 
        string $status, 
        ?array $requestData = null, 
        ?array $responseData = null, 
        ?string $errorMessage = null,
        ?\Carbon\Carbon $syncedAt = null
    ): void {
        HighwayCarrierSyncLog::create([
            'organization_id' => $carrier->organization_id,
            'carrier_id' => $carrier->id,
            'sync_type' => $syncType,
            'highway_carrier_id' => $carrier->highway_carrier_id,
            'status' => $status,
            'request_data' => $requestData,
            'response_data' => $responseData,
            'error_message' => $errorMessage,
            'synced_at' => $syncedAt,
        ]);
    }
}