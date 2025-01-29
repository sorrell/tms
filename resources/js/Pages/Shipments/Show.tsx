import { buttonVariants } from '@/Components/ui/button';
import { Skeleton } from '@/Components/ui/skeleton';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function Show({ shipment }: { shipment: any }) {
    return (
        <AuthenticatedLayout>
            <Head title="Shipments" />
            <div className="flex flex-col w-full px-8 space-y-8">
                <span className="text-2xl font-bold">
                    Viewing Shipment {shipment.id}
                </span>
                <Skeleton className="w-full h-[400px]" />
            </div>
        </AuthenticatedLayout>
    );
}
