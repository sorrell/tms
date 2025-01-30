import ShipmentList from '@/Components/Shipments/ShipmentList/ShipmentList';
import { buttonVariants } from '@/Components/ui/button';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function Index({ shipments }: { shipments: any }) {
    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {
                    title: 'Shipments',
                },
            ]}
        >
            <Head title="Shipments" />
            <div className="flex justify-end px-8">
                <Link
                    href={route('shipments.create')}
                    className={buttonVariants({ variant: 'default' })}
                >
                    Create Shipment
                </Link>
            </div>
            <div className="mx-auto flex flex-col gap-4">
                {/* {shipments.map((shipment: any) => (
                    <Link
                        href={route('shipments.show', shipment.id)}
                        key={shipment.id}
                        className={buttonVariants({ variant: 'outline' })}
                    >
                        View Shipment {shipment.id}
                    </Link>
                ))} */}
                <ShipmentList />
            </div>
        </AuthenticatedLayout>
    );
}
