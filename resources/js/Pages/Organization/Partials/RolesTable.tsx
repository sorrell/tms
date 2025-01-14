import PrimaryButton from '@/Components/PrimaryButton';
import { Button } from '@/Components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import { Organization, Role } from '@/types/organization';
import { useForm } from '@inertiajs/react';
import { MoreHorizontal } from 'lucide-react';

import { useToast } from '@/hooks/use-toast';

export default function RolesTable({
    roles,
    organization,
}: {
    roles: Role[];
    organization: Organization;
}) {
    const { delete: destroy, post } = useForm();
    const { toast } = useToast();

    return (
        <Table>
            <TableCaption>A list of organization roles.</TableCaption>
            <TableHeader>
                <TableRow>
                    <TableHead>Name</TableHead>
                    <TableHead className="hidden md:table-cell">
                        Users
                    </TableHead>
                    <TableHead className="hidden md:table-cell">
                        Permissions
                    </TableHead>
                    <TableHead className="text-right">Actions</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                {roles.map((role) => (
                    <TableRow key={role.id}>
                        <TableCell>{role.name}</TableCell>

                        <TableCell className="hidden md:table-cell">
                            {role.users?.map((user) => (
                                <div key={user.id}>{user.name}</div>
                            ))}
                        </TableCell>

                        <TableCell className="hidden md:table-cell">
                            {role.permissions?.map((permission) => (
                                <div key={permission.id}>{permission.name}</div>
                            ))}
                        </TableCell>

                        <TableCell className="flex justify-end space-x-4">
                            <PrimaryButton>Edit</PrimaryButton>
                            <DropdownMenu>
                                <DropdownMenuTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        className="h-8 w-8 p-0"
                                    >
                                        <span className="sr-only">
                                            Open menu
                                        </span>
                                        <MoreHorizontal className="h-4 w-4" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end">
                                    <DropdownMenuLabel>
                                        Actions
                                    </DropdownMenuLabel>
                                    <DropdownMenuItem
                                        onClick={() => {
                                            destroy(
                                                route(
                                                    'organizations.permissions.destroy-role',
                                                    {
                                                        organization:
                                                            organization.id,
                                                        role: role.id,
                                                    },
                                                ),
                                                {
                                                    onSuccess: () => {
                                                        toast({
                                                            description:
                                                                'Role deleted',
                                                        });
                                                    },
                                                },
                                            );
                                        }}
                                    >
                                        Delete Role
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </TableCell>
                    </TableRow>
                ))}
            </TableBody>
        </Table>
    );
}
