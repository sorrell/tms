<?php

namespace App\Http\Requests\Shipments;

use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'shipper_ids' => ['required', 'array'],
            'shipper_ids.*' => ['required', 'exists:shippers,id'],

            'shipment_number' => ['nullable', 'string'],

            'weight' => ['nullable', 'numeric'],
            'trip_distance' => ['nullable', 'numeric'],
            'trailer_type_id' => ['nullable', 'exists:trailer_types,id'],
            'trailer_temperature_range' => ['nullable', 'boolean'],
            'trailer_temperature' => ['nullable', 'numeric'],
            'trailer_temperature_maximum' => ['nullable', 'numeric'],

            'carrier_id' => ['required', 'exists:carriers,id'],
            'stops' => ['required', 'array'],
            'stops.*.stop_type' => ['required', 'in:pickup,delivery'],
            'stops.*.facility_id' => ['required', 'exists:facilities,id'],
            'stops.*.appointment.datetime' => ['required', 'date'],
            'stops.*.special_instructions' => ['nullable', 'string'],
            'stops.*.reference_numbers' => ['nullable', 'string'],
            'stops.*.stop_number' => ['required', 'integer'],
        ];
    }
}
