<?php

namespace App\Actions\Subscriptions;

use App\Enums\Permission;
use App\Enums\Subscriptions\SubscriptionType;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class NewStartupSubscription
{
    use AsAction;

    public function handle()
    {
        // Check if billing is enabled
        if (!config('subscriptions.enable_billing')) {
            abort(403, 'Billing is disabled');
        }

        $organization = current_organization();

        // For $0 startup subscriptions, create directly without checkout
        $subscription = $organization
            ->newSubscription(
                SubscriptionType::STARTUP->value,
                config('subscriptions.startup.monthly_price_id')
            )
            ->quantity(1, config('subscriptions.startup.monthly_price_id'))
            ->create();

        // Redirect to success page
        return redirect()->route('products-list', ['success' => true, 'type' => 'startup']);
    }

    public function asController(ActionRequest $request)
    {
        return $this->handle();
    }

    public function authorize(ActionRequest $request): bool
    {
        return config('subscriptions.enable_billing') && $request->user()->can(Permission::ORGANIZATION_BILLING);
    }
} 