<?php

namespace App\Actions\Highway\CarrierSync;

use App\Models\Carriers\Carrier;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateCarrierFromHighwayDataAction
{
    use AsAction;

    public function handle(Carrier $carrier, array $highwayData): array
    {
        try {
            $updateData = [];
            $fieldsUpdated = [];

            // Map Highway data to LoadPartner fields
            $fieldMappings = $this->getFieldMappings();

            foreach ($fieldMappings as $highwayField => $carrierField) {
                $highwayValue = data_get($highwayData, $highwayField);
                
                if ($highwayValue !== null && $highwayValue !== '') {
                    $currentValue = $carrier->{$carrierField};
                    
                    // Only update if value is different
                    if ($currentValue !== $highwayValue) {
                        $updateData[$carrierField] = $highwayValue;
                        $fieldsUpdated[] = $carrierField;
                    }
                }
            }

            // Always update Highway sync timestamp
            $updateData['highway_last_synced_at'] = now();
            $updateData['highway_sync_status'] = 'monitoring';

            if (!empty($updateData)) {
                $carrier->update($updateData);
            }

            return [
                'success' => true,
                'message' => count($fieldsUpdated) > 0 
                    ? 'Carrier updated with ' . count($fieldsUpdated) . ' field(s)'
                    : 'Carrier sync timestamp updated, no data changes needed',
                'fields_updated' => $fieldsUpdated,
                'update_data' => $updateData
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update carrier: ' . $e->getMessage(),
                'fields_updated' => [],
                'update_data' => []
            ];
        }
    }

    public function authorize(): bool
    {
        return auth()->user()->can('manage carriers');
    }

    /**
     * Define field mappings between Highway data and LoadPartner carrier fields
     */
    private function getFieldMappings(): array
    {
        return [
            // Highway field => LoadPartner field
            'insurance.status' => 'insurance_status',
            'insurance.expiry_date' => 'insurance_expiry',
            'authority.status' => 'authority_status',
            'safety.rating' => 'safety_rating',
            'safety.last_inspection' => 'last_inspection_date',
            'contact.phone' => 'phone',
            'contact.email' => 'email',
            'address.street' => 'address',
            'address.city' => 'city',
            'address.state' => 'state',
            'address.zip' => 'zip_code',
            'company_name' => 'name',
            'dba_name' => 'dba_name',
            'mc_number' => 'mc_number',
            'dot_number' => 'dot_number',
        ];
    }

    /**
     * Process date fields to ensure proper format
     */
    private function processDateField(?string $dateValue): ?\Carbon\Carbon
    {
        if (!$dateValue) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($dateValue);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Process enum fields to ensure valid values
     */
    private function processEnumField(?string $value, array $allowedValues): ?string
    {
        if (!$value) {
            return null;
        }

        return in_array($value, $allowedValues) ? $value : null;
    }
}