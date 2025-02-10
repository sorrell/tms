import CarrierList from '@/Components/Carriers/CarrierList/CarrierList';
import { buttonVariants } from '@/Components/ui/button';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Carrier } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import CarrierDetails from './Partials/CarrierDetails';

export default function Index() {
    const { carrier } = usePage().props;
    const [selectedCarrier, setSelectedCarrier] = useState<Carrier | undefined>(
        carrier as Carrier,
    );

    useEffect(() => {
        if (selectedCarrier) {
            const url = new URL(window.location.href);
            url.searchParams.set('carrier_id', selectedCarrier.id.toString());
            window.history.pushState({}, '', url.toString());
        } else {
            const url = new URL(window.location.href);
            url.searchParams.delete('carrier_id');
            window.history.pushState({}, '', url.toString());
        }
    }, [selectedCarrier]);

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {
                    title: 'Carriers',
                },
            ]}
        >
            <Head title="Carriers" />
            <div className="flex justify-end px-8">
                <Link
                    href="#"
                    className={buttonVariants({ variant: 'default' })}
                    disabled={true}
                >
                    Create Carrier
                </Link>
            </div>
            <div className="mx-auto flex max-w-screen-2xl flex-col gap-4">
                <CarrierList onSelect={setSelectedCarrier} />
                <CarrierDetails carrier={selectedCarrier} />
            </div>
        </AuthenticatedLayout>
    );
} 