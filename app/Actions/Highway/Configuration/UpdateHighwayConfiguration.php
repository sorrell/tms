<?php

namespace App\Actions\Highway\Configuration;

use App\Models\Organizations\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateHighwayConfiguration
{
    use AsAction;

    public function handle(Organization $organization, array $data): bool
    {
        $configData = [
            'api_key' => Crypt::encryptString($data['api_key']),
            'environment' => $data['environment'],
            'auto_sync_enabled' => $data['auto_sync_enabled'] ?? true,
            'sync_frequency' => $data['sync_frequency'] ?? 'daily',
        ];

        $organization->highwayConfiguration()->updateOrCreate(
            ['organization_id' => $organization->id],
            $configData
        );

        return true;
    }

    public function asController(ActionRequest $request)
    {
        $organization = $request->user()->organization;
        
        $this->handle($organization, $request->validated());

        return back()->with('success', 'Highway configuration updated successfully.');
    }

    public function rules(): array
    {
        return [
            'api_key' => ['required', 'string', 'min:20'],
            'environment' => ['required', 'in:staging,production'],
            'auto_sync_enabled' => ['boolean'],
            'sync_frequency' => ['string', 'in:daily,weekly'],
        ];
    }

    public function authorize(): bool
    {
        return auth()->user()->can('manage organization settings');
    }
}