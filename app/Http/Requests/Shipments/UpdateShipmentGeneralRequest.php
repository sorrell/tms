<?php

namespace App\Http\Requests\Shipments;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShipmentGeneralRequest extends FormRequest
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
            'trailer_temperature' => ['nullable', 'numeric'],
            'trailer_temperature_maximum' => [
                'nullable',
                'numeric',
                'required_if:trailer_temperature_range,true',
                'gt:trailer_temperature',
            ],
            'trailer_temperature_range' => ['boolean'],
            'trailer_type_id' => ['nullable', 'exists:trailer_types,id'],
            'weight' => ['nullable', 'numeric'],
            'trip_distance' => ['nullable', 'numeric'],
        ];
    }
}
