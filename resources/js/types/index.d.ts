export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    current_organization_id: number;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    app: {
        name: string;
    };
    permissions: {
        [key: string]: boolean;
    };
};
