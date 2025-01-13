import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Organization, OrganizationInvite } from '@/types/organization';
import { Head } from '@inertiajs/react';
import InvitesTable from './Partials/InvitesTable';
import UsersTable from './Partials/UsersTable';

export default function Show({
    organization,
    invites,
}: {
    organization: Organization;
    invites: OrganizationInvite[];
}) {
    return (
        <AuthenticatedLayout>
            <Head title="Organization" />

            <h1>Organization {organization.name}</h1>

            <UsersTable users={organization.users} />

            <InvitesTable invites={invites} />
        </AuthenticatedLayout>
    );
}
