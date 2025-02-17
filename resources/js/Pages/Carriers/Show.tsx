import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Carrier } from '@/types';
import { Head } from '@inertiajs/react';
import CarrierDetails from './Partials/CarrierDetails';

export default function Show({ carrier }: { carrier: Carrier }) {
    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {
                    title: 'Carriers',
                    href: route('carriers.index'),
                },
                {
                    title: carrier?.name,
                },
            ]}
        >
            <Head title="Carrier" />
            <div className="mx-auto flex max-w-screen-2xl flex-col gap-4">
                <CarrierDetails carrier={carrier} />
            </div>
        </AuthenticatedLayout>
    );
}
