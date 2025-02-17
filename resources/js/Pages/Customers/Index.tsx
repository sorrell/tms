import CustomerList from '@/Components/Customers/CustomerList/CustomerList';
import InputError from '@/Components/InputError';
import { buttonVariants } from '@/Components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog';
import { Input } from '@/Components/ui/input';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Customer } from '@/types';
import { Head, router } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { useState } from 'react';

export default function Index() {
    const [isOpen, setIsOpen] = useState(false);
    const [customerName, setCustomerName] = useState('');
    const [errors, setErrors] = useState<Record<string, string>>({});
    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setErrors({});
        router.post(
            route('customers.store'),
            {
                name: customerName,
            },
            {
                onSuccess: () => setIsOpen(false),
                onError: (errors) => {
                    console.log(errors);
                    setErrors(errors);
                },
            },
        );
    };

    const setSelectedCustomer = (customer: Customer) => {
        router.visit(route('customers.show', { customer }));
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {
                    title: 'Customers',
                },
            ]}
        >
            <Head title="Customers" />
            <div className="mx-auto max-w-screen-2xl">
                <div className="mb-4 flex justify-end px-8 md:mb-0">
                    <button
                        onClick={() => setIsOpen(true)}
                        className={buttonVariants({ variant: 'default' })}
                    >
                        <Plus /> Add Customer
                    </button>
                </div>
                <div className="mx-auto flex flex-col gap-4">
                    <CustomerList onSelect={setSelectedCustomer} />
                </div>
            </div>

            <Dialog open={isOpen} onOpenChange={setIsOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Add New Customer</DialogTitle>
                    </DialogHeader>
                    <form onSubmit={handleSubmit} className="space-y-4">
                        <Input
                            placeholder="Customer name"
                            value={customerName}
                            onChange={(e) => setCustomerName(e.target.value)}
                            required
                        />
                        {errors.name && <InputError message={errors.name} />}
                        <DialogFooter>
                            <button
                                type="button"
                                onClick={() => setIsOpen(false)}
                                className={buttonVariants({
                                    variant: 'outline',
                                })}
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                className={buttonVariants({
                                    variant: 'default',
                                })}
                            >
                                Add
                            </button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </AuthenticatedLayout>
    );
}
