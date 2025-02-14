'use client';

import { Contact } from '@/types';
import { ColumnDef } from '@tanstack/react-table';

export const columns: ColumnDef<Contact>[] = [
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
        accessorKey: 'email',
        header: 'Email',
        cell: ({ row }) => {
            return (
                <div className="flex flex-col space-y-2">
                    {row.original.email}
                </div>
            );
        },
    },
    {
        accessorKey: 'mobile_phone',
        header: 'Mobile Phone',
        cell: ({ row }) => {
            return (
                <div className="flex flex-col space-y-2">
                    {row.original.mobile_phone}
                </div>
            );
        },
    },
    {
        accessorKey: 'office_phone',
        header: 'Office Phone',
        cell: ({ row }) => {
            return (
                <div className="flex flex-col space-y-2">
                    {row.original.office_phone}
                    {row.original.office_phone_extension &&
                        ` x${row.original.office_phone_extension}`}
                </div>
            );
        },
    },
];
