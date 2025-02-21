import ShipmentList from '@/Components/Shipments/ShipmentList/ShipmentList';
import { buttonVariants } from '@/Components/ui/button';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { Plus } from 'lucide-react';

export default function Index() {
    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {
                    title: 'Shipments',
                },
            ]}
        >
            <Head title="Shipments" />
            <div className="mx-auto flex max-w-screen-2xl justify-end px-8">
                <Link
                    href={route('shipments.create')}
                    className={buttonVariants({ variant: 'default' })}
                >
                    <Plus /> Create Shipment
                </Link>
            </div>
            <div className="mx-auto flex flex-col gap-4">
                <ShipmentList />
            </div>
        </AuthenticatedLayout>
    );
}
