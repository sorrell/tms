'use client';

import { Carrier } from '@/types';
import { ColumnDef } from '@tanstack/react-table';

export const columns: ColumnDef<Carrier>[] = [
    {
        accessorKey: 'name',
        header: 'Name',
        cell: ({ row }) => {
            return (
                <div className="flex flex-col space-y-2">
                    {row.original.name}
                </div>
            );
        },
    },
];
