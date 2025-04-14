import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Organization,
    OrganizationInvite,
    Permission,
    Role,
} from '@/types/organization';
import { Head, usePage } from '@inertiajs/react';
import InvitesTable from './Partials/InvitesTable';
import RolesTable from './Partials/RolesTable';
import UsersTable from './Partials/UsersTable';

export default function Users({
    organization,
    invites,
}: {
    organization: Organization;
    invites: OrganizationInvite[];
}) {
    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {
                    title: 'Organization',
                },
                {
                    title: 'Users',
                }
            ]}
        >
            <Head title="Organization" />

            <h1 className="mx-4 mb-4 text-2xl">
                Organization {organization.name} users
            </h1>

            <div className="mx-4 mb-4">
                <UsersTable
                    users={organization.users}
                    organization={organization}
                />
            </div>

            <div className="mx-4 mb-4">
                <h1 className="mb-4 text-xl">Pending Invites</h1>
                <InvitesTable invites={invites} />
            </div>
        </AuthenticatedLayout>
    );
}
