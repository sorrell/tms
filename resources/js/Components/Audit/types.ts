export interface AuditChange {
    field: string;
    field_name: string;
    old_value: unknown;
    new_value: unknown;
}

export interface AuditEntry {
    id: number;
    event: string;
    auditable_type?: string;
    auditable_id?: number;
    entity_type?: string;
    entity_name?: string;
    user: {
        id: number;
        name: string;
        email: string;
    } | null;
    old_values: Record<string, unknown>;
    new_values: Record<string, unknown>;
    changes: AuditChange[];
    created_at: string;
    created_at_human: string;
    ip_address?: string;
    user_agent?: string;
}
