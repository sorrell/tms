'use client';

import { Button } from '@/Components/ui/button';
import { Contact } from '@/types';
import { ColumnDef } from '@tanstack/react-table';
import { Copy, Pencil, Trash } from 'lucide-react';

export const columns: ColumnDef<
    Contact & {
        onDelete?: (contact: Contact) => void;
        onEdit?: (contact: Contact) => void;
    }
>[] = [
    {
        accessorKey: 'contact_type',
        header: 'Type',
        cell: ({ row }) => {
            return <div>{row.original.contact_type}</div>;
        },
    },
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
        accessorKey: 'title',
        header: 'Title',
        cell: ({ row }) => {
            return <div>{row.original.title ?? '-'}</div>;
        },
    },
    {
        accessorKey: 'email',
        header: 'Email',
        cell: ({ row }) => {
            return (
                <div className="flex flex-col space-y-2">
                    {row.original.email ? (
                        <button
                            onClick={() =>
                                navigator.clipboard.writeText(
                                    row.original.email,
                                )
                            }
                            className="text-left transition-colors hover:text-blue-600"
                            title="Click to copy email"
                        >
                            <Copy className="mr-2 inline h-4 w-4 text-muted-foreground" />
                            {row.original.email}
                        </button>
                    ) : (
                        '-'
                    )}
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
                    {row.original.mobile_phone ? (
                        <button
                            onClick={() =>
                                navigator.clipboard.writeText(
                                    row.original.mobile_phone,
                                )
                            }
                            className="text-left transition-colors hover:text-blue-600"
                            title="Click to copy mobile phone"
                        >
                            <Copy className="mr-2 inline h-4 w-4 text-muted-foreground" />
                            {row.original.mobile_phone}
                        </button>
                    ) : (
                        '-'
                    )}
                </div>
            );
        },
    },
    {
        accessorKey: 'office_phone',
        header: 'Office Phone',
        cell: ({ row }) => {
            const phoneWithExtension = row.original.office_phone_extension
                ? `${row.original.office_phone} x${row.original.office_phone_extension}`
                : row.original.office_phone;

            return (
                <div className="flex flex-col space-y-2">
                    {row.original.office_phone ? (
                        <button
                            onClick={() =>
                                navigator.clipboard.writeText(
                                    phoneWithExtension,
                                )
                            }
                            className="text-left transition-colors hover:text-blue-600"
                            title="Click to copy office phone"
                        >
                            <Copy className="mr-2 inline h-4 w-4 text-muted-foreground" />
                            {row.original.office_phone}
                            {row.original.office_phone_extension &&
                                ` x${row.original.office_phone_extension}`}
                        </button>
                    ) : (
                        '-'
                    )}
                </div>
            );
        },
    },
    {
        accessorKey: 'actions',
        header: () => <div className="text-right">Actions</div>,
        cell: ({ row }) => {
            return (
                <div className="flex justify-end gap-1">
                    <Button
                        variant="outline"
                        size="icon"
                        onClick={() => row.original.onEdit?.(row.original)}
                    >
                        <Pencil className="h-4 w-4" />
                    </Button>
                    <Button
                        variant="destructive"
                        size="icon"
                        onClick={() => row.original.onDelete?.(row.original)}
                    >
                        <Trash className="h-4 w-4" />
                    </Button>
                </div>
            );
        },
    },
];
