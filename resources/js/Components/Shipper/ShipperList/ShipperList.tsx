import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Skeleton } from '@/Components/ui/skeleton';
import { Shipper } from '@/types';
import axios from 'axios';
import { Search } from 'lucide-react';
import { useCallback, useEffect, useRef, useState } from 'react';
import { columns } from './Columns';
import { DataTable } from './DataTable';

export default function ShipperList({
    onSelect,
}: {
    onSelect: (shipper: Shipper) => void;
}) {
    const [data, setData] = useState<Shipper[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [searchTerm, setSearchTerm] = useState('');
    const inputRef = useRef<HTMLInputElement>(null);

    const getShippers = useCallback((searchTerm?: string) => {
        const getData = (): Promise<Shipper[]> => {
            return axios
                .get(route('shippers.search'), {
                    params: {
                        query: searchTerm,
                        with: [],
                    },
                })
                .then((response) => response.data);
        };

        setIsLoading(true);

        getData()
            .then((shippers) => {
                setData(shippers);
                setIsLoading(false);
            })
            .catch((error) => {
                console.error('Error fetching shippers:', error);
                setIsLoading(false);
            });
    }, []);

    useEffect(() => {
        if (!isLoading) {
            inputRef.current?.focus();
        }
    }, [isLoading]);

    useEffect(() => {
        getShippers();
    }, [getShippers]);

    return (
        <div className="mx-auto flex w-full max-w-screen-2xl flex-col gap-2">
            <div className="flex gap-1">
                <Input
                    ref={inputRef}
                    className="max-w-md"
                    placeholder="Search shippers"
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    onKeyDown={(e) => {
                        if (e.key === 'Enter') {
                            getShippers(searchTerm);
                        }
                    }}
                />
                <Button onClick={() => getShippers(searchTerm)}>
                    <Search className="h-4 w-4" />
                </Button>
            </div>
            {isLoading ? (
                <>
                    <Skeleton className="mx-auto h-[200px] w-1/2 rounded-md" />
                </>
            ) : (
                <>
                    <DataTable
                        columns={columns}
                        data={data}
                        onSelect={onSelect}
                    />
                </>
            )}
        </div>
    );
}
