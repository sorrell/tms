import ShipperList from '@/Components/Shipper/ShipperList/ShipperList';
import { buttonVariants } from '@/Components/ui/button';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Shipper } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { useState } from 'react';
import ShipperDetails from './Partials/ShipperDetails';

export default function Index() {
    const [selectedShipper, setSelectedShipper] = useState<Shipper | undefined>(
        undefined,
    );

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {
                    title: 'Shippers',
                },
            ]}
        >
            <Head title="Shippers" />
            <div className="flex justify-end px-8">
                <Link
                    href={route('shippers.create')}
                    className={buttonVariants({ variant: 'default' })}
                >
                    Create Shipper
                </Link>
            </div>
            <div className="mx-auto flex flex-col gap-4 max-w-screen-2xl">
                <ShipperList onSelect={setSelectedShipper} />
                <ShipperDetails shipper={selectedShipper} />
            </div>
        </AuthenticatedLayout>
    );
}
