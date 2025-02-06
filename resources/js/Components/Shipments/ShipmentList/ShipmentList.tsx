import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Skeleton } from '@/Components/ui/skeleton';
import { Shipment } from '@/types';
import axios from 'axios';
import { Search } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';
import { columns } from './Columns';
import { DataTable } from './DataTable';

export default function ShipmentList() {
    const [data, setData] = useState<Shipment[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [searchTerm, setSearchTerm] = useState('');
    const inputRef = useRef<HTMLInputElement>(null);

    const getData = (): Promise<Shipment[]> => {
        return axios
            .get(route('shipments.search'), {
                params: {
                    query: searchTerm,
                    with: [
                        'carrier',
                        'shippers',
                        'stops',
                        'trailer_type',
                        'trailer_size',
                    ],
                },
            })
            .then((response) => response.data);
    };

    const getShipments = () => {
        setIsLoading(true);
        getData()
            .then((shipments) => {
                setData(shipments);
                setIsLoading(false);
            })
            .catch((error) => {
                console.error('Error fetching shipments:', error);
                setIsLoading(false);
            });
    };

    useEffect(() => {
        getShipments();
    }, []);

    useEffect(() => {
        if (!isLoading) {
            inputRef.current?.focus();
        }
    }, [isLoading]);

    if (isLoading) {
        return;
    }

    return (
        <div className="mx-auto flex w-full max-w-screen-2xl flex-col gap-2">
            <div className="flex gap-1">
                <Input
                    ref={inputRef}
                    className="max-w-md"
                    placeholder="Search shipments"
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    onKeyDown={(e) => {
                        if (e.key === 'Enter') {
                            getShipments();
                        }
                    }}
                />
                <Button onClick={getShipments}>
                    <Search className="h-4 w-4" />
                </Button>
            </div>
            {isLoading ? (
                <>
                    <Skeleton className="mx-auto h-[200px] w-1/2 rounded-md" />
                </>
            ) : (
                <>
                    <DataTable columns={columns} data={data} />
                </>
            )}
        </div>
    );
}
