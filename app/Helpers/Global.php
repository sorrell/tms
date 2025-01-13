<?php

use App\Models\Organizations\Organization;

function current_organization_id() : int
{
    // TODO - add a check for the "Context" current_organization_id
    return auth()->user()->current_organization_id;
}

function current_organization() : Organization
{
    return Organization::find(current_organization_id());
}
