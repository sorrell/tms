<?php

namespace App\Actions\Highway\CarrierSync;

use App\Models\Carriers\Carrier;
use Lorisleiva\Actions\Concerns\AsAction;

class ValidateCarrierForHighwaySyncAction
{
    use AsAction;

    public function handle(Carrier $carrier): array
    {
        $missingFields = [];
        $warnings = [];

        // Required fields for Highway
        if (empty($carrier->dot_number)) {
            $missingFields[] = 'DOT Number';
        }

        if (empty($carrier->name)) {
            $missingFields[] = 'Company Name';
        }

        // Recommended fields
        if (empty($carrier->mc_number)) {
            $warnings[] = 'MC Number is recommended for better tracking';
        }

        if (empty($carrier->address)) {
            $warnings[] = 'Address is recommended for location tracking';
        }

        if (empty($carrier->phone)) {
            $warnings[] = 'Phone number is recommended for contact purposes';
        }

        if (empty($carrier->email)) {
            $warnings[] = 'Email is recommended for communication';
        }

        // Check if carrier already has Highway ID but not marked as monitored
        if ($carrier->highway_carrier_id && !$carrier->highway_monitored) {
            $warnings[] = 'Carrier has Highway ID but is not marked as monitored';
        }

        $valid = empty($missingFields);

        $message = '';
        if (!$valid) {
            $message = 'Missing required fields: ' . implode(', ', $missingFields);
        } elseif (!empty($warnings)) {
            $message = 'Validation passed with warnings: ' . implode('; ', $warnings);
        } else {
            $message = 'Carrier is valid for Highway sync';
        }

        return [
            'valid' => $valid,
            'message' => $message,
            'missing_fields' => $missingFields,
            'warnings' => $warnings,
            'can_sync' => $valid,
        ];
    }

    public function authorize(): bool
    {
        return auth()->user()->can('view carriers');
    }
}