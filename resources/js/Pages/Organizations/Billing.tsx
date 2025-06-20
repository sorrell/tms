import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Organization } from '@/types/organization';
import { Head } from '@inertiajs/react';
import BillingForm from './Partials/BillingForm';
import { ShipmentUsage, Subscription } from '@/types/shipment';

export default function Billing({
    organization,
    subscription,
    shipmentUsage,
}: {
    organization: Organization;
    subscription?: Subscription;
    shipmentUsage?: ShipmentUsage;
}) {
    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {
                    title: 'Organization',
                },
                {
                    title: 'Billing',
                },
            ]}
        >
            <Head title="Organization Billing" />

            <h1 className="mx-4 mb-4 text-2xl">
                Organization {organization.name} billing
            </h1>

            <div className="mx-4 mb-4">
                <BillingForm
                    organization={organization}
                    subscription={subscription}
                    shipmentUsage={shipmentUsage}
                />
            </div>
        </AuthenticatedLayout>
    );
}
