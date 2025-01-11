import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Show({ organization }) {
    return (
        <AuthenticatedLayout>
            <Head title="Organization" />

            <h1>Organization {organization.name}</h1>

            <div>
                <h2>Members</h2>

                <ul>
                    {organization.users.map((user) => (
                        <li
                            key={user.id}
                            style={
                                user.id === organization.owner_id
                                    ? { fontWeight: 'bold', color: 'red' }
                                    : {}
                            }
                        >
                            {user.name}{' '}
                            {user.id === organization.owner_id && '(Owner)'}
                        </li>
                    ))}
                </ul>
            </div>
        </AuthenticatedLayout>
    );
}
