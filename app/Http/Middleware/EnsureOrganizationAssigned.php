<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationAssigned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && ! $request->user()->current_organization_id) {
            // assign them to the first organization in their list
            $firstOrg = $request->user()->organizations->first();

            if ($firstOrg) {
                $request->user()->update(['current_organization_id' => $firstOrg->id]);
            } else {
                // If no they don't have an org, make them create one
                return redirect()->route('organizations.create');
            }

        }
        return $next($request);
    }
}
