import { User } from '.';

export interface Organization {
    id: number;
    name: string;
    users: User[];
    owner_id: number;
    company_name?: string;
    company_address?: string;
    company_city?: string;
    company_state?: string;
    company_zip?: string;
    company_phone?: string;
    company_email?: string;
    accounting_contact_email?: string;
    accounting_contact_phone?: string;
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

export interface Role {
    id: number;
    name: string;
    organization_id: number;
    permissions: Permission[];
    users: User[];
    created_at: string;
    updated_at: string;
}

export interface Permission {
    id: number;
    name: string;
    label: string;
}

export interface IntegrationSetting {
    id: number;
    key: string;
    value: string;
    provider: string;
    expose_to_frontend: boolean;
    created_at: string;
    updated_at: string;
}

export interface GlobalIntegrationSetting {
    key: string;
    value?: string;
    provider?: string;
    expose_to_frontend: boolean;
    label: string;
    description: string;
}
