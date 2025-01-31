'use client';

import { Shipment } from '@/types';
import { ColumnDef } from '@tanstack/react-table';

export const columns: ColumnDef<Shipment>[] = [
    {
        accessorKey: 'id',
        header: 'ID',
    },
    {
        accessorKey: 'shippers',
        header: 'Shippers',
        cell: ({ row }) => {
            const shippers = row.original.shippers;
            return shippers.map(shipper => shipper.name).join(', ');
        },
    },
    {
        accessorKey: 'carrier',
        header: 'Carrier',
        cell: ({ row }) => {
            const carrier = row.original.carrier;
            return carrier ? carrier.name : 'N/A';
        },
    },
    // {
    //     accessorKey: 'stops',
    //     header: 'Stops',
    // },
];
