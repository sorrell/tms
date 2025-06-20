<?php

namespace App\Actions\Organizations;

use App\Enums\Subscriptions\SubscriptionType;
use App\Models\Organizations\Organization;
use Lorisleiva\Actions\Concerns\AsAction;

class CheckSeatLimits
{
    use AsAction;

    public function handle(Organization $organization): void
    {
        if ($organization->subscribed(SubscriptionType::USER_SEAT->value)) {
            $subscription = $organization->subscription(SubscriptionType::USER_SEAT->value);
            $maxSeats = $subscription->quantity;

            // Count current organization members
            $currentMembers = $organization->users()->count();
            
            // Count pending invites
            $pendingInvites = $organization->invites()->count();

            $totalUsed = $currentMembers + $pendingInvites;

            if ($totalUsed >= $maxSeats) {
                throw new \Symfony\Component\HttpFoundation\Exception\BadRequestException('Not enough seats available. You are using ' . $totalUsed . ' out of ' . $maxSeats . ' seats. Please upgrade your subscription to invite more users.');
            }
            return;
        }

        // Check if organization has a startup subscription (1 seat only, cannot be upgraded)
        if ($organization->subscribed(SubscriptionType::STARTUP->value)) {
            $currentMembers = $organization->users()->count();
            $pendingInvites = $organization->invites()->count();
            $totalUsed = $currentMembers + $pendingInvites;

            if ($totalUsed >= 1) {
                throw new \Symfony\Component\HttpFoundation\Exception\BadRequestException(
                    'Startup plan is limited to a single seat. ' .
                    'Please upgrade to Premium to invite more users.'
                );
            }
            return;
        }

        // No subscription
        throw new \Symfony\Component\HttpFoundation\Exception\BadRequestException('Organization does not have an active subscription.');
    }

    /**
     * Get seat usage information for an organization
     */
    public function getSeatUsage(Organization $organization): array
    {
        // Check for premium subscription first (USER_SEAT has priority over STARTUP)
        if ($organization->subscribed(SubscriptionType::USER_SEAT->value)) {
            $subscription = $organization->subscription(SubscriptionType::USER_SEAT->value);
            $maxSeats = $subscription->quantity;
            $currentMembers = $organization->users()->count();
            $pendingInvites = $organization->invites()->count();
            $totalUsed = $currentMembers + $pendingInvites;

            return [
                'current_members' => $currentMembers,
                'pending_invites' => $pendingInvites,
                'total_used' => $totalUsed,
                'max_seats' => $maxSeats,
                'has_available_seats' => $totalUsed < $maxSeats,
                'has_subscription' => true,
                'is_startup_plan' => false,
            ];
        }

        // Handle startup subscription only if no premium subscription exists
        if ($organization->subscribed(SubscriptionType::STARTUP->value)) {
            $currentMembers = $organization->users()->count();
            $pendingInvites = $organization->invites()->count();
            $totalUsed = $currentMembers + $pendingInvites;

            return [
                'current_members' => $currentMembers,
                'pending_invites' => $pendingInvites,
                'total_used' => $totalUsed,
                'max_seats' => 1,
                'has_available_seats' => $totalUsed < 1,
                'has_subscription' => true,
                'is_startup_plan' => true,
            ];
        }

        // No subscription
        return [
            'current_members' => $organization->users()->count(),
            'pending_invites' => $organization->invites()->count(),
            'total_used' => $organization->users()->count() + $organization->invites()->count(),
            'max_seats' => 0,
            'has_available_seats' => false,
            'has_subscription' => false,
            'is_startup_plan' => false,
        ];
    }
} 