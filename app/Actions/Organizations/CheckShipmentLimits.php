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
        // Only check load limits for startup subscriptions
        if (!$organization->subscription(SubscriptionType::STARTUP->value)) {
            return;
        }

        $subscription = $organization->subscription(SubscriptionType::STARTUP->value);
        
        if (!in_array($subscription->stripe_status, ['active', 'trialing'])) {
            return;
        }

        $weeklyLimit = config('subscriptions.startup.weekly_load_limit', 10);

        // Count shipments created this week
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
        // Check for premium subscription first (USER_SEAT has unlimited shipments)
        if ($organization->subscribed(SubscriptionType::USER_SEAT->value)) {
            $subscription = $organization->subscription(SubscriptionType::USER_SEAT->value);
            
            if ($subscription && in_array($subscription->stripe_status, ['active', 'trialing'])) {
                // Premium users have unlimited shipments
                return [
                    'is_startup_plan' => false,
                    'weekly_limit' => null, // null indicates unlimited
                    'shipments_this_week' => 0, // Not relevant for premium
                    'remaining_shipments' => null, // null indicates unlimited
                    'week_start' => null,
                    'week_end' => null,
                ];
            }
        }

        // Handle startup subscription only if no premium subscription exists
        $subscription = $organization->subscription(SubscriptionType::STARTUP->value);
        
        if (!in_array($subscription->stripe_status, ['active', 'trialing'])) {
            return [
                'is_startup_plan' => false,
                'weekly_limit' => 0,
                'shipments_this_week' => 0,
                'remaining_shipments' => 0,
                'week_start' => null,
                'week_end' => null,
            ];
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