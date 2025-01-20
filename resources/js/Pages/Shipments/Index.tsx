import { buttonVariants } from '@/Components/ui/button';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function Index() {
    return (
        <AuthenticatedLayout>
            <Head title="Shipments" />
            <div className="flex justify-end px-8">
                <Link
                    href={route('shipments.create')}
                    className={buttonVariants({ variant: 'default' })}
                >
                    Create Shipment
                </Link>
            </div>
        </AuthenticatedLayout>
    );
}
