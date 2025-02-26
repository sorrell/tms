import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Facility } from '@/types';
import { Head } from '@inertiajs/react';
import FacilityDetails from './Partials/FacilityDetails';

export default function Show({ facility }: { facility: Facility }) {
    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {
                    title: 'Facilities',
                    href: route('facilities.index'),
                },
                {
                    title: facility?.name,
                },
            ]}
        >
            <Head title="Facility" />
            <div className="mx-auto flex max-w-screen-2xl flex-col gap-4">
                <FacilityDetails facility={facility} />
            </div>
        </AuthenticatedLayout>
    );
}
