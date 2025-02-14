import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Skeleton } from '@/Components/ui/skeleton';
import { Contact } from '@/types';
import axios from 'axios';
import { Search } from 'lucide-react';
import { useCallback, useEffect, useRef, useState } from 'react';
import { columns } from './Columns';
import { DataTable } from './DataTable';

export default function ContactList({
    contactForId,
    contactForType,
}: {
    contactForId: number;
    contactForType: string;
}) {
    const [data, setData] = useState<Contact[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [searchTerm, setSearchTerm] = useState('');
    const inputRef = useRef<HTMLInputElement>(null);

    const getContacts = useCallback((searchTerm?: string) => {
        const getData = (): Promise<Contact[]> => {
            return axios
                .get(route('contacts.search'), {
                    params: {
                        query: searchTerm,
                        with: [],
                        filters: [
                            {
                                name: 'contact_for_id',
                                value: contactForId,
                            },
                            {
                                name: 'contact_for_type',
                                value: contactForType,
                            },
                        ],
                    },
                })
                .then((response) => response.data);
        };

        setIsLoading(true);

        getData()
            .then((contacts) => {
                setData(contacts);
                setIsLoading(false);
            })
            .catch((error) => {
                console.error('Error fetching contacts:', error);
                setIsLoading(false);
            });
    }, []);

    useEffect(() => {
        if (!isLoading) {
            inputRef.current?.focus();
        }
    }, [isLoading]);

    useEffect(() => {
        getContacts();
    }, [getContacts]);

    return (
        <div className="mx-auto flex w-full max-w-screen-2xl flex-col gap-2">
            <div className="flex gap-1">
                <Input
                    ref={inputRef}
                    className="max-w-md"
                    placeholder="Search contacts"
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    onKeyDown={(e) => {
                        if (e.key === 'Enter') {
                            getContacts(searchTerm);
                        }
                    }}
                />
                <Button onClick={() => getContacts(searchTerm)}>
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
                    />
                </>
            )}
        </div>
    );
}
