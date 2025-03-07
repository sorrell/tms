export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    current_organization_id: number;
    organizations: Organization[];
    profile_photo_url?: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
        permissions: {
            [key: string]: boolean;
        };
    };
    app: {
        name: string;
    };
};

export interface Location {
    id: number;
    name: string;
    address_line_1: string;
    address_line_2: string;
    address_city: string;
    address_state: string;
    address_zipcode: string;
    selectable_label: string;
}

export interface Contact {
    id: number;
    contact_type?: string;
    title?: string;
    name: string;
    email?: string;
    mobile_phone?: string;
    office_phone?: string;
    office_phone_extension?: string;
    contact_for_id: number;
    contact_for_type: string;
    contact_for?: {
        id: number;
    };
}

export interface Facility {
    id: number;
    name: string;
    location?: Location;
    location_id?: number;
}

export interface Carrier {
    id: number;
    name: string;
    mc_number: string;
    dot_number: string;
    contact_email: string;
    contact_phone: string;

    physical_location_id: number;
    billing_email: string;
    billing_phone: string;
    billing_location_id: number;

    billing_location?: Location;
    physical_location?: Location;
    safer_report?: CarrierSaferReport;
}

export interface CarrierSaferReport {
    id: number;
    dot_number: string;
    created_at: string;
    report: {
        name: string;
        dba: string;
        full_address: string;
        address: {
            street: string;
            city: string;
            state: string;
            zip: string;
        };
        general?: {
            carrier?: {
                legalName?: string;
                dbaName?: string;
                dotNumber?: string;
                statusCode?: string;
                safetyRating?: string;
                totalPowerUnits?: number;
                totalDrivers?: number;
                mcs150Outdated?: string;
                crashTotal?: number;
                fatalCrash?: number;
                injCrash?: number;
                driverInsp?: number;
                driverOosInsp?: number;
                driverOosRate?: number;
                driverOosRateNationalAverage?: string;
                vehicleInsp?: number;
                vehicleOosInsp?: number;
                vehicleOosRate?: number;
                vehicleOosRateNationalAverage?: string;
            };
        };
    };
    is_full_report: boolean;
    carrier?: Carrier;
}

export interface Customer {
    id: number;
    name: string;
}

export interface ShipmentStop {
    id: number | null;
    shipment_id: number;
    facility?: Facility | null;
    facility_id?: number | null;
    stop_type: StopType;
    stop_number: number;
    special_instructions: string;
    reference_numbers: string;
    eta: string;
    arrived_at: string;
    loaded_unloaded_at: string;
    left_at: string;
    appointment_at: string;
    appointment_end_at: string;
    appointment_type: string;
}

export interface Shipment {
    id: number;
    shipment_number: string;
    customers: Customer[];
    carrier: Carrier;
    driver?: Contact;
    stops: ShipmentStop[];
    weight: number;
    trip_distance: number;
    trailer_type_id: number;
    trailer_size_id: number;
    trailer_temperature_range: boolean;
    trailer_temperature: number;
    trailer_temperature_maximum?: number;
    lane?: string;
    next_stop?: ShipmentStop;
    trailer_type?: TrailerType;
    trailer_size?: TrailerSize;
    state_label: string;
    state: ShipmentState;
}

export interface Note {
    id: number;
    user_id: number;
    note: string;
    created_at: string;
    updated_at: string;
    user: User | null;
}

export interface TrailerType {
    id: number;
    name: string;
}

export interface TrailerSize {
    id: number;
    name: string;
}

export interface CarrierBounce {
    id: number;
    carrier_id: number;
    shipment_id: number;
    bounce_type: string;
    reason: string;
    created_at: string;
    bounced_by: number;

    bounced_by_user?: User;
    shipment?: Shipment;
    carrier?: Carrier;
}
