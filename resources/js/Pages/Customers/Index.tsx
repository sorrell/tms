import CustomerList from '@/Components/Customers/CustomerList/CustomerList';
import { buttonVariants } from '@/Components/ui/button';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Customer } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import CustomerDetails from './Partials/CustomerDetails';

export default function Index() {
    const { customer } = usePage().props;
    const [selectedCustomer, setSelectedCustomer] = useState<
        Customer | undefined
    >(customer as Customer);

    useEffect(() => {
        if (selectedCustomer) {
            const url = new URL(window.location.href);
            url.searchParams.set('customer_id', selectedCustomer.id.toString());
            window.history.pushState({}, '', url.toString());
        } else {
            const url = new URL(window.location.href);
            url.searchParams.delete('customer_id');
            window.history.pushState({}, '', url.toString());
        }
    }, [selectedCustomer]);

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
                <CustomerDetails customer={selectedCustomer} />
            </div>
        </AuthenticatedLayout>
    );
}
