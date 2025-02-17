import CarrierList from '@/Components/Carriers/CarrierList/CarrierList';
import { buttonVariants } from '@/Components/ui/button';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Carrier } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import { Plus } from 'lucide-react';

export default function Index() {
    const setSelectedCarrier = (carrier: Carrier) => {
        router.visit(route('carriers.show', { carrier }));
    };

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
                    <Plus /> Create Carrier
                </Link>
            </div>
            <div className="mx-auto flex max-w-screen-2xl flex-col gap-4">
                <CarrierList onSelect={setSelectedCarrier} />
            </div>
        </AuthenticatedLayout>
    );
}
