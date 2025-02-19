import CarrierList from '@/Components/Carriers/CarrierList/CarrierList';
import InputError from '@/Components/InputError';
import { Button } from '@/Components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog';
import { Input } from '@/Components/ui/input';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Carrier } from '@/types';
import { Head, router } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { useState } from 'react';
import CarrierCreateDialog from './Partials/CarrierCreateDialog';

export default function Index() {
    const [isOpen, setIsOpen] = useState(false);


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
            <div className="mx-auto max-w-screen-2xl">
                <div className="flex justify-end px-8">
                    <Button
                        type="button"
                        onClick={() => {
                            setIsOpen(true);
                        }}
                    >
                        <Plus /> Create Carrier
                    </Button>
                </div>
                <div className="mx-auto flex max-w-screen-2xl flex-col gap-4">
                    <CarrierList onSelect={setSelectedCarrier} />
                </div>
            </div>
            <CarrierCreateDialog isOpen={isOpen} setIsOpen={setIsOpen} />
        </AuthenticatedLayout>
    );
}
