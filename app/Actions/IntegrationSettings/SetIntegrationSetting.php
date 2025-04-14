<?php

namespace App\Actions\IntegrationSettings;

use App\Models\Organizations\IntegrationSetting;
use App\Rules\BetterBoolean;
use Exception;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class SetIntegrationSetting
{
    use AsAction;

    /**
     * Set an integration setting value
     */
    public function handle(string $key, $value, ?string $provider = null, bool $encrypted = false, bool $exposeToFrontend = false) : IntegrationSetting
    {

        $organization = current_organization();

        if (!$organization) {
            throw new Exception("A current organization is required to set integration settings");
        }

        $setting = $organization->integration_settings()->where('key', $key)->first();

        if (!$setting) {
            $setting = new IntegrationSetting();
            $setting->key = $key;
            $setting->is_encrypted = $encrypted;
            $setting->value = $value;   // Must come after is_encrypted so encryption is applied correctly
            $setting->provider = $provider;
            $setting->expose_to_frontend = $exposeToFrontend;
            $organization->integration_settings()->save($setting);
        } else {
            $oldKey = $setting->key;
            $oldProvider = $setting->provider;
            $setting->provider = $provider;
            $setting->is_encrypted = $encrypted;
            $setting->value = $value;   // Must come after is_encrypted so encryption is applied correctly
            $setting->expose_to_frontend = $exposeToFrontend;
            $setting->save();
            // Clear cache for old values too
            ClearIntegrationCaches::run($oldKey, $oldProvider);
        }
        // clear cache for new values
        ClearIntegrationCaches::run($key, $provider);

        return $setting;
    }

    public function asController(ActionRequest $request) 
    {
        return $this->handle(
            key: $request->validated('key'),
            value: $request->validated('value'),
            provider: $request->validated('provider'),
            encrypted: $request->validated('encrypted'),
            exposeToFrontend: $request->validated('expose_to_frontend')
        );
    }

    public function htmlResponse(IntegrationSetting $setting)
    {
        return redirect()->back()->with('success', 'Integration setting updated');
    }

    public function rules()
    {
        return [
            'key' => 'required|string',
            'value' => 'required|string',
            'provider' => 'nullable|string',
            'encrypted' => ['nullable', new BetterBoolean],
            'expose_to_frontend' => ['nullable', new BetterBoolean]
        ];
    }
}