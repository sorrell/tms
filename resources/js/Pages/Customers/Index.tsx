import CustomerList from '@/Components/Customers/CustomerList/CustomerList';
import { buttonVariants } from '@/Components/ui/button';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Customer } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import { Plus } from 'lucide-react';

export default function Index() {
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
                    <Link
                        href={route('customers.create')}
                        disabled={true}
                        className={buttonVariants({ variant: 'default' })}
                    >
                        <Plus /> Add Customer
                    </Link>
                </div>
                <div className="mx-auto flex flex-col gap-4">
                    <CustomerList onSelect={setSelectedCustomer} />
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
