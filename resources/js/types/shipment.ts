export interface ShipmentUsage {
    is_startup_plan: boolean;
    weekly_limit: number | null; // null indicates unlimited
    shipments_this_week: number;
    remaining_shipments: number | null; // null indicates unlimited
    week_start: string | null;
    week_end: string | null;
}

export interface Subscription {
    id: number;
    type: string;
    stripe_status: string;
    quantity: number;
    trial_ends_at?: string;
    ends_at?: string;
} 