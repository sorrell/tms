import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import { User } from '@/types';

export default function UsersTable({ users }: { users: User[] }) {
    return (
        <Table>
            <TableCaption>A list of organization users.</TableCaption>
            <TableHeader>
                <TableRow>
                    <TableHead>Email</TableHead>
                    <TableHead>Name</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                {users.map((user) => (
                    <TableRow key={user.id}>
                        <TableCell>{user.email}</TableCell>
                        <TableCell>{user.name}</TableCell>
                    </TableRow>
                ))}
            </TableBody>
        </Table>
    );
}
