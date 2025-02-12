import CustomerList from '@/Components/Customers/CustomerList/CustomerList';
import { buttonVariants } from '@/Components/ui/button';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Customer } from '@/types';
import { Head, Link, router } from '@inertiajs/react';

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
            <div className="flex justify-end px-8">
                <Link
                    href={route('customers.create')}
                    disabled={true}
                    className={buttonVariants({ variant: 'default' })}
                >
                    Create Customer
                </Link>
            </div>
            <div className="mx-auto flex max-w-screen-2xl flex-col gap-4">
                <CustomerList onSelect={setSelectedCustomer} />
            </div>
        </AuthenticatedLayout>
    );
}
