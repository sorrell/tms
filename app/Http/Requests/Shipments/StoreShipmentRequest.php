<?php

namespace App\Http\Requests\Shipments;

use App\Enums\TemperatureUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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

            'weight' => ['nullable', 'numeric'],
            'trip_miles' => ['nullable', 'numeric'],
            'trailer_type_id' => ['nullable', 'exists:trailer_types,id'],
            'trailer_temperature_range' => ['nullable', 'boolean'],
            'trailer_temperature_minimum' => ['nullable', 'numeric'],
            'trailer_temperature_maximum' => ['nullable', 'numeric'],
            'trailer_temperature_unit' => ['nullable', Rule::enum(TemperatureUnit::class)],

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
