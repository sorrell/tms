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
    {
        accessorKey: 'mc_number',
        header: 'MC Number',
        cell: ({ row }) => {
            return <div>{row.original.mc_number}</div>;
        },
    },
    {
        accessorKey: 'dot_number',
        header: 'DOT Number',
        cell: ({ row }) => {
            return <div>{row.original.dot_number}</div>;
        },
    },
    {
        accessorKey: 'physical_location',
        header: 'Location',
        cell: ({ row }) => {
            return (
                <div>{row.original.physical_location?.selectable_label}</div>
            );
        },
    },
];
