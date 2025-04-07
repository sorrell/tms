import { Carrier } from '@/types';
import { router } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { columns } from '../Carriers/CarrierList/Columns';
import { DataTable } from '../Carriers/CarrierList/DataTable';
import { Skeleton } from '../ui/skeleton';
import DashboardCard from './DashboardCard';

export default function RecentCarriersCard() {
    const [carriers, setCarriers] = useState<Carrier[]>([]);
    const [loading, setLoading] = useState<boolean>(true);

    useEffect(() => {
        const fetchRecentCarriers = async () => {
            try {
                setLoading(true);
                fetch(route('dashboard.cards.recent-carriers'), {
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                    },
                }).then(async (response) => {
                    setCarriers(await response.json());
                    setLoading(false);
                });
            } catch (error) {
                console.error('Error fetching recent carriers:', error);
            } finally {
                setLoading(false);
            }
        };

        fetchRecentCarriers();
    }, [setCarriers]);

    return (
        <DashboardCard
            title="Recent Carriers"
            cols={3}
            className="md:overflow-x-scroll"
        >
            {loading ? (
                <Skeleton className="h-[32px] w-full" />
            ) : (
                <DataTable
                    columns={columns}
                    data={carriers}
                    onSelect={(carrier) =>
                        router.visit(route('carriers.show', { carrier }))
                    }
                />
            )}
        </DashboardCard>
    );
}
