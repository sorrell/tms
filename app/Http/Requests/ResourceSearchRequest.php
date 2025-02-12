<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResourceSearchRequest extends FormRequest
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
            'query' => 'nullable|string',
            'ids' => 'nullable|array',
            'with' => 'nullable|array',
            'filters' => 'nullable|array',
            'filters.*.name' => 'required|string',
            'filters.*.value' => 'required|string',
        ];
    }
}
