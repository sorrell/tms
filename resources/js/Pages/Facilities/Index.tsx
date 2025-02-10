import FacilityList from '@/Components/Facilities/FacilityList/FacilityList';
import { buttonVariants } from '@/Components/ui/button';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Facility } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import FacilityDetails from './Partials/FacilityDetails';

export default function Index() {
    const { facility } = usePage().props;
    const [selectedFacility, setSelectedFacility] = useState<Facility | undefined>(
        facility as Facility,
    );

    useEffect(() => {
        if (selectedFacility) {
            const url = new URL(window.location.href);
            url.searchParams.set('facility_id', selectedFacility.id.toString());
            window.history.pushState({}, '', url.toString());
        } else {
            const url = new URL(window.location.href);
            url.searchParams.delete('facility_id');
            window.history.pushState({}, '', url.toString());
        }
    }, [selectedFacility]);

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {
                    title: 'Facilities',
                },
            ]}
        >
            <Head title="Facilities" />
            <div className="flex justify-end px-8">
                <Link
                    href="#"
                    className={buttonVariants({ variant: 'default' })}
                    disabled={true}
                >
                    Create Facility
                </Link>
            </div>
            <div className="mx-auto flex max-w-screen-2xl flex-col gap-4">
                <FacilityList onSelect={setSelectedFacility} />
                <FacilityDetails facility={selectedFacility} />
            </div>
        </AuthenticatedLayout>
    );
}
