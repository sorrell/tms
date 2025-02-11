import ShipperList from '@/Components/Shipper/ShipperList/ShipperList';
import { buttonVariants } from '@/Components/ui/button';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Shipper } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import ShipperDetails from './Partials/ShipperDetails';

export default function Index() {
    const { shipper } = usePage().props;
    const [selectedShipper, setSelectedShipper] = useState<Shipper | undefined>(
        shipper as Shipper,
    );

    useEffect(() => {
        if (selectedShipper) {
            const url = new URL(window.location.href);
            url.searchParams.set('shipper_id', selectedShipper.id.toString());
            window.history.pushState({}, '', url.toString());
        } else {
            const url = new URL(window.location.href);
            url.searchParams.delete('shipper_id');
            window.history.pushState({}, '', url.toString());
        }
    }, [selectedShipper]);

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
                    disabled={true}
                    className={buttonVariants({ variant: 'default' })}
                >
                    Create Shipper
                </Link>
            </div>
            <div className="mx-auto flex max-w-screen-2xl flex-col gap-4">
                <ShipperList onSelect={setSelectedShipper} />
                <ShipperDetails shipper={selectedShipper} />
            </div>
        </AuthenticatedLayout>
    );
}
