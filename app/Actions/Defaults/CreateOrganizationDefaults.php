<?php

namespace App\Actions\Defaults;

use App\Models\Accounting\AccessorialType;
use App\Models\Accounting\CarrierRateType;
use App\Models\Accounting\CustomerRateType;
use App\Models\Permissions\Role;
use App\Models\Shipments\TrailerSize;
use App\Models\Shipments\TrailerType;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateOrganizationDefaults {
    use AsAction;

    
    public function handle(int $organizationId) {
        
        /** Default roles */
        Role::create([
            'name' => 'default',
            'organization_id' => $organizationId
        ]);


        /** Accessorial types */
        AccessorialType::create(['name' => 'Fee Type', 'organization_id' => $organizationId]);
        AccessorialType::create(['name' => 'Pre-pull', 'organization_id' => $organizationId]);
        AccessorialType::create(['name' => 'Per Diem', 'organization_id' => $organizationId]);
        AccessorialType::create(['name' => 'Demurrage', 'organization_id' => $organizationId]);
        AccessorialType::create(['name' => 'Detention', 'organization_id' => $organizationId]);
        AccessorialType::create(['name' => 'Yard Storage', 'organization_id' => $organizationId]);
        AccessorialType::create(['name' => 'Chassis Use', 'organization_id' => $organizationId]);
        AccessorialType::create(['name' => 'Reefer Fee', 'organization_id' => $organizationId]);
        AccessorialType::create(['name' => 'Drop and Hook', 'organization_id' => $organizationId]);
        AccessorialType::create(['name' => 'Layover', 'organization_id' => $organizationId]);
        AccessorialType::create(['name' => 'Congestion Fee', 'organization_id' => $organizationId]);
        AccessorialType::create(['name' => 'Fuel Surcharge', 'organization_id' => $organizationId]);
        AccessorialType::create(['name' => 'Overweight', 'organization_id' => $organizationId]);
        AccessorialType::create(['name' => 'Lumper Fee', 'organization_id' => $organizationId]);
        AccessorialType::create(['name' => 'Port Fees', 'organization_id' => $organizationId]);
        AccessorialType::create(['name' => 'Chassis Split', 'organization_id' => $organizationId]);

        /** Carrier rate type */
        CarrierRateType::create(['name' => 'Flat', 'organization_id' => $organizationId]);
        CarrierRateType::create(['name' => 'Per Mile', 'organization_id' => $organizationId]);

        /** Customer rate types */
        CustomerRateType::create(['name' => 'Flat', 'organization_id' => $organizationId]);
        CustomerRateType::create(['name' => 'Per Mile', 'organization_id' => $organizationId]);

        /** Trailer related */
        TrailerType::create(['name' => 'Stepdeck', 'organization_id' => $organizationId]);
        TrailerType::create(['name' => 'Dry Van', 'organization_id' => $organizationId]);
        TrailerType::create(['name' => 'Reefer', 'organization_id' => $organizationId]);
        TrailerType::create(['name' => 'Flatbed', 'organization_id' => $organizationId]);

        TrailerSize::create(['name'=> '53\'', 'organization_id' => $organizationId]);
        TrailerSize::create(['name'=> '48\'', 'organization_id' => $organizationId]);

        /** Contact types */
        // TODO - https://linear.app/loadpartner/issue/TMS-133/change-contact-types-to-the-new-default-format-and-remove-the-enums

        /** Default document folders */
        // TODO - change document folders to use a "default folders" approach
        // https://linear.app/loadpartner/issue/TMS-120/default-document-folders-table-instead-of-enum-merge-types-together
    }
}