<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrganizationInviteRequest;
use App\Http\Requests\UpdateOrganizationInviteRequest;
use App\Models\Organization;
use App\Models\OrganizationInvite;

class OrganizationInviteController extends Controller
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrganizationInviteRequest $request, Organization $organization)
    {
        $invite = OrganizationInvite::create([
            'email' => $request->email,
            'organization_id' => $organization->id
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(OrganizationInvite $invite, Organization $organization)
    {
        // TODO - accept invite page
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrganizationInvite $invite, Organization $organization)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrganizationInviteRequest $request, OrganizationInvite $invite, Organization $organization)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrganizationInvite $invite, Organization $organization)
    {
        //
    }

    /**
     * Accept the organization invite and add current user to the org
     */
    public function accept(OrganizationInvite $invite)
    {
        // TODO - accept invite
    }
}
