<?php

namespace App\Actions\Subscriptions;

use App\Enums\Permission;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class RedirectToBillingPortal
{
    use AsAction;

    public function handle()
    {
        $organization = current_organization();

        return $organization->redirectToBillingPortal(
            route('organizations.billing', $organization)
        );
    }

    public function asController(ActionRequest $request)
    {
        return $this->handle();
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can(Permission::ORGANIZATION_BILLING);
    }
} 