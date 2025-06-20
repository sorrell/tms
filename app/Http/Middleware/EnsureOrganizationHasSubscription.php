<?php

namespace App\Http\Middleware;

use App\Enums\Subscriptions\SubscriptionType;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationHasSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip subscription checks if billing is disabled
        if (!config('subscriptions.enable_billing')) {
            return $next($request);
        }

        $organization = current_organization();
        
        if ($organization?->subscribed(SubscriptionType::USER_SEAT->value) || 
            $organization?->subscribed(SubscriptionType::STARTUP->value)) {
            return $next($request);
        }

        return redirect()->route('products-list');
    }
}
