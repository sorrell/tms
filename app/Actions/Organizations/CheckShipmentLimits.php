<?php

namespace App\Actions\Organizations;

use App\Enums\Subscriptions\SubscriptionType;
use App\Models\Organizations\Organization;
use App\Models\Shipments\Shipment;
use Lorisleiva\Actions\Concerns\AsAction;

class CheckShipmentLimits
{
    use AsAction;

    public function handle(Organization $organization): void
    {

        $subscription = $organization->subscription(SubscriptionType::STARTUP->value);

        if (!in_array($subscription->stripe_status, ['active', 'trialing'])
        || $this->billingDisabled()
        || $this->hasActivePremiumSubscription($organization)) {
            return;
        }

        $weeklyLimit = config('subscriptions.startup.weekly_load_limit', 10);
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $shipmentsThisWeek = Shipment::where('organization_id', $organization->id)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->count();

        if ($shipmentsThisWeek >= $weeklyLimit) {
            throw new \Symfony\Component\HttpFoundation\Exception\BadRequestException(
                "Weekly shipment limit reached. You have created {$shipmentsThisWeek} shipments this week. " .
                "Startup plan is limited to {$weeklyLimit} shipments per week. " .
                "Please upgrade to Premium to create unlimited shipments."
            );
        }
    }

    /**
     * Get load usage information for an organization
     */
    public function getShipmentUsage(Organization $organization): array
    {
        // If billing is disabled, return unlimited usage
        if ($this->billingDisabled()
        || $this->hasActivePremiumSubscription($organization)) {
            return $this->getUnlimitedUsage();
        }

        // Check startup subscription limits
        return $this->getStartupUsage($organization);
    }

    private function getUnlimitedUsage(): array
    {
        return [
            'is_startup_plan' => false,
            'weekly_limit' => null, // null indicates unlimited
            'shipments_this_week' => 0, // Not relevant when unlimited
            'remaining_shipments' => null, // null indicates unlimited
            'week_start' => null,
            'week_end' => null,
        ];
    }

    private function billingDisabled(): bool
    {
        return !config('cashier.key') || config('subscriptions.enable_billing') === false;
    }

    private function hasActivePremiumSubscription(Organization $organization): bool
    {
        if (!$organization->subscribed(SubscriptionType::USER_SEAT->value)) {
            return false;
        }

        $subscription = $organization->subscription(SubscriptionType::USER_SEAT->value);
        
        return $subscription && in_array($subscription->stripe_status, ['active', 'trialing']);
    }

    private function getStartupUsage(Organization $organization): array
    {
        $subscription = $organization->subscription(SubscriptionType::STARTUP->value);
        
        // If no startup subscription or not active, treat as unlimited
        if (!$subscription || !in_array($subscription->stripe_status, ['active', 'trialing'])) {
            return $this->getUnlimitedUsage();
        }

        $weeklyLimit = config('subscriptions.startup.weekly_load_limit', 10);
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $shipmentsThisWeek = Shipment::where('organization_id', $organization->id)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->count();

        return [
            'is_startup_plan' => true,
            'weekly_limit' => $weeklyLimit,
            'shipments_this_week' => $shipmentsThisWeek,
            'remaining_shipments' => max(0, $weeklyLimit - $shipmentsThisWeek),
            'week_start' => $startOfWeek->toDateString(),
            'week_end' => $endOfWeek->toDateString(),
        ];
    }
} 