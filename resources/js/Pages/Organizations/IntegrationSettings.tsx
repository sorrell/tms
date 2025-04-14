import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Organization, IntegrationSetting } from '@/types/organization';
import { Head, usePage } from '@inertiajs/react';
import IntegrationSettingsTable from './Partials/IntegrationSettingsTable';

export default function IntegrationSettingsPage({
    organization,
    integrationSettings,
}: {
    organization: Organization;
    integrationSettings: IntegrationSetting[];
}) {

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { title: 'Organization' },
                { title: 'Integration Settings' },
            ]}
        >
            <Head title="Integration Settings" />
            <h1 className="mx-4 mb-4 text-2xl">
                Organization {organization.name} Integration Settings
            </h1>
            <div className="mx-4 mb-4">
                <IntegrationSettingsTable
                    integrationSettings={integrationSettings}
                    organization={organization}
                />
            </div>

        </AuthenticatedLayout>
    );
} 