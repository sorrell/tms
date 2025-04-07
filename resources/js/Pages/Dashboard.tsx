import DashboardCard from '@/Components/Dashboard/DashboardCard';
import RecentShipmentsCard from '@/Components/Dashboard/RecentShipmentsCard';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Skeleton } from '@/Components/ui/skeleton';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Dashboard() {
    const getGreeting = () => {
        const hour = new Date().getHours();
        const day = new Date().toLocaleDateString('en-US', { weekday: 'long' });

        let timeGreeting;
        if (hour < 12) timeGreeting = 'Good morning';
        else if (hour < 17) timeGreeting = 'Good afternoon';
        else timeGreeting = 'Good evening';

        return `${timeGreeting}! Happy ${day}!`;
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {
                    title: 'Dashboard',
                },
            ]}
        >
            <Head title="Dashboard" />

            <div className="py-12 mx-auto md:grid md:grid-cols-3 flex flex-wrap max-w-7xl gap-4 sm:px-6 lg:px-8">
                <DashboardCard title="Last 7 days">
                    <Skeleton className='w-full h-[64px]' />
                </DashboardCard>
                <DashboardCard title="Last 30 days">
                    <Skeleton className='w-full h-[64px]' />
                </DashboardCard>
                <DashboardCard title="Year-to-date">
                    <Skeleton className='w-full h-[64px]' />
                </DashboardCard>
                
                <RecentShipmentsCard />
                
            </div>
        </AuthenticatedLayout>
    );
}
