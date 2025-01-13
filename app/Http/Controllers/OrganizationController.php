<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use App\Models\Organizations\Organization;
use App\Models\Organizations\OrganizationUser;
use Inertia\Inertia;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Organization/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrganizationRequest $request)
    {
        $org = Organization::create([
            'name' => $request->name,
            'owner_id' => $request->user()->id,
        ]);

        $user = auth()->user();

        $orgMembership = OrganizationUser::create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
        ]);

        $user->update([
            'current_organization_id' => $org->id,
        ]);

        return redirect()->route('dashboard');
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization)
    {
        return Inertia::render('Organization/Show', [
            'organization' => $organization->load('owner', 'users'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrganizationRequest $request, Organization $organization)
    {
        $organization->update($request->validated());

        return redirect()->route('organizations.edit', $organization);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization)
    {
        //
    }
}
