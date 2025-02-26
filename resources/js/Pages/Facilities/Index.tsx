import FacilityList from '@/Components/Facilities/FacilityList/FacilityList';
import { Button } from '@/Components/ui/button';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Facility } from '@/types';
import { Head, router } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { useState } from 'react';
import FacilityCreateDialog from './Partials/FacilityCreateDialog';

export default function Index() {
    const [isOpen, setIsOpen] = useState(false);

    const setSelectedFacility = (facility: Facility) => {
        router.visit(route('facilities.show', { facility }));
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {
                    title: 'Facilities',
                },
            ]}
        >
            <Head title="Facilities" />
            <div className="mx-auto max-w-screen-2xl">
                <div className="flex justify-end px-8">
                    <Button
                        type="button"
                        onClick={() => {
                            setIsOpen(true);
                        }}
                    >
                        <Plus /> Create Facility
                    </Button>
                </div>
                <div className="mx-auto flex max-w-screen-2xl flex-col gap-4">
                    <FacilityList onSelect={setSelectedFacility} />
                </div>
            </div>
            <FacilityCreateDialog isOpen={isOpen} setIsOpen={setIsOpen} />
        </AuthenticatedLayout>
    );
}
