import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Organization, OrganizationInvite, Role } from '@/types/organization';
import { Head } from '@inertiajs/react';
import InvitesTable from './Partials/InvitesTable';
import RolesTable from './Partials/RolesTable';
import UsersTable from './Partials/UsersTable';

export default function Show({
    organization,
    invites,
    roles,
}: {
    organization: Organization;
    invites: OrganizationInvite[];
    roles: Role[];
}) {
    return (
        <AuthenticatedLayout>
            <Head title="Organization" />

            <h1 className="mx-4 mb-4 text-2xl">
                Organization {organization.name}
            </h1>

            <Tabs defaultValue="users">
                <TabsList className="mx-auto grid w-full max-w-[500px] grid-cols-2">
                    <TabsTrigger value="users">Users</TabsTrigger>
                    <TabsTrigger value="roles">Roles</TabsTrigger>
                </TabsList>
                <TabsContent value="users">
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
                </TabsContent>
                <TabsContent value="roles">
                    <div className="mx-4 mb-4">
                        <RolesTable roles={roles} organization={organization} />
                    </div>
                </TabsContent>
            </Tabs>
        </AuthenticatedLayout>
    );
}
