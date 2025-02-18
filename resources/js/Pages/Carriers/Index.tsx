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

export default function Index() {
    const [isOpen, setIsOpen] = useState(false);
    const [carrierName, setCarrierName] = useState('');
    const [errors, setErrors] = useState<Record<string, string>>({});

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setErrors({});
        router.post(
            route('carriers.store'),
            {
                name: carrierName,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    setIsOpen(false);
                    setCarrierName('');
                },
                onError: (errors) => {
                    setErrors(errors);
                },
            },
        );
    };

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
                            setCarrierName('');
                        }}
                    >
                        <Plus /> Create Carrier
                    </Button>
                </div>
                <div className="mx-auto flex max-w-screen-2xl flex-col gap-4">
                    <CarrierList onSelect={setSelectedCarrier} />
                </div>
            </div>
            <Dialog open={isOpen} onOpenChange={setIsOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Create Carrier</DialogTitle>
                    </DialogHeader>
                    <form onSubmit={handleSubmit} className="space-y-4">
                        <Input
                            placeholder="Carrier name"
                            value={carrierName}
                            onChange={(e) => setCarrierName(e.target.value)}
                        />
                        {errors.name && <InputError message={errors.name} />}
                        <DialogFooter>
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => setIsOpen(false)}
                            >
                                Cancel
                            </Button>
                            <Button type="submit">Create</Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </AuthenticatedLayout>
    );
}
