import { User } from '.';

export interface Organization {
    id: number;
    name: string;
    users: User[];
    owner_id: number;
}

export interface OrganizationInvite {
    id: number;
    organization_id;
    email: string;
    created_at: string;
    updated_at: string;
    expire_at: string;
    accepted_at: boolean;
    accepted_by_id: number;
    code: string;
}
