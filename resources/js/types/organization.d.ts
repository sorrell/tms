import { User } from '.';

export interface Organization {
    id: number;
    name: string;
    users: User[];
}
