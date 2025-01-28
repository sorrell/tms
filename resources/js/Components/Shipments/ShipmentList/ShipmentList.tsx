import { Shipment } from '@/types';
import { columns } from './Columns';
import { DataTable } from './DataTable';
import axios from 'axios';
import { useEffect, useState } from 'react';
import { Skeleton } from '@/Components/ui/skeleton';

function getData(): Promise<Shipment[]> {
    return axios.get(route('shipments.search'), {
        params: {
            query: '',
        },
    }).then(response => response.data);
}

export default function ShipmentList() {
    const [data, setData] = useState<Shipment[]>([]);
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        getData()
            .then(shipments => {
                setData(shipments);
                setIsLoading(false);
            })
            .catch(error => {
                console.error('Error fetching shipments:', error);
                setIsLoading(false);
            });
    }, []);
    

    if (isLoading) {
        return <Skeleton className="w-1/2 mx-auto h-[200px] rounded-md" />;
    }

    return (
        <div className="mx-auto">
            <DataTable columns={columns} data={data} />
        </div>
    );
}
