import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Customer } from '@/types';
import { Head } from '@inertiajs/react';
import CustomerDetails from './Partials/CustomerDetails';

export default function Show({ customer }: { customer: Customer }) {
    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {
                    title: 'Customers',
                    href: route('customers.index'),
                },
                {
                    title: customer?.name,
                },
            ]}
        >
            <Head title="Customer" />
            <div className="mx-auto flex max-w-screen-2xl flex-col gap-4">
                <CustomerDetails customer={customer} />
            </div>
        </AuthenticatedLayout>
    );
}
