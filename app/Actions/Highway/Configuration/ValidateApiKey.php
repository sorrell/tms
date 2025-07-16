<?php

namespace App\Actions\Highway\Configuration;

use Lorisleiva\Actions\Concerns\AsAction;

class ValidateApiKey
{
    use AsAction;

    public function handle(string $apiKey): array
    {
        // Basic format validation
        if (strlen($apiKey) < 20) {
            return [
                'valid' => false,
                'message' => 'API key must be at least 20 characters long'
            ];
        }

        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $apiKey)) {
            return [
                'valid' => false,
                'message' => 'API key contains invalid characters'
            ];
        }

        // Additional validation could include:
        // - Checking key format patterns
        // - Validating against known Highway key formats
        // - Testing key against Highway API

        return [
            'valid' => true,
            'message' => 'API key format is valid'
        ];
    }

    public function authorize(): bool
    {
        return auth()->user()->can('manage organization settings');
    }
}