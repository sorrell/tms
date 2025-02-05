import { Skeleton } from '@/Components/ui/skeleton';
import { Shipment } from '@/types';
import axios from 'axios';
import { useEffect, useState } from 'react';
import { columns } from './Columns';
import { DataTable } from './DataTable';

function getData(): Promise<Shipment[]> {
    return axios
        .get(route('shipments.search'), {
            params: {
                query: '',
                with: ['carrier', 'shippers', 'stops'],
            },
        })
        .then((response) => response.data);
}

export default function ShipmentList() {
    const [data, setData] = useState<Shipment[]>([]);
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        getData()
            .then((shipments) => {
                setData(shipments);
                setIsLoading(false);
            })
            .catch((error) => {
                console.error('Error fetching shipments:', error);
                setIsLoading(false);
            });
    }, []);

    if (isLoading) {
        return <Skeleton className="mx-auto h-[200px] w-1/2 rounded-md" />;
    }

    return (
        <div className="mx-auto">
            <DataTable columns={columns} data={data} />
        </div>
    );
}
