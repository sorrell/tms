import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Organization,
    OrganizationInvite,
    Permission,
    Role,
} from '@/types/organization';
import { Head, usePage } from '@inertiajs/react';
import RolesTable from './Partials/RolesTable';

export default function Show({
    organization,
    invites,
    roles,
    permissions,
}: {
    organization: Organization;
    invites: OrganizationInvite[];
    roles: Role[];
    permissions: Permission[];
}) {
    const userPermissions = usePage().props.auth.permissions;


    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {
                    title: 'Organization',
                },
                {
                    title: 'Roles',
                }
            ]}
        >
            <Head title="Organization" />

            <h1 className="mx-4 mb-4 text-2xl">
                Organization {organization.name} roles
            </h1>

            <div className="mx-4 mb-4">
                <RolesTable
                    roles={roles}
                    organization={organization}
                    permissions={permissions}
                />
            </div>
        </AuthenticatedLayout>
    );
}
