import { Card, CardContent } from '@/Components/ui/card';
import { Shipment } from '@/types';
import { useEffect, useState } from 'react';
import AuditTimeline from './AuditTimeline';

interface AuditChange {
    field: string;
    field_name: string;
    old_value: unknown;
    new_value: unknown;
}

interface AuditEntry {
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

interface ShipmentAuditHistoryProps {
    shipment: Shipment;
}

export default function ShipmentAuditHistory({
    shipment,
}: ShipmentAuditHistoryProps) {
    const [audits, setAudits] = useState<AuditEntry[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const fetchAudits = async () => {
            try {
                setLoading(true);
                const response = await fetch(
                    route('shipments.audit-history', shipment.id),
                );

                if (!response.ok) {
                    throw new Error('Failed to fetch audit history');
                }

                const data = await response.json();
                setAudits(data.audits || []);
            } catch (err) {
                setError(
                    err instanceof Error ? err.message : 'An error occurred',
                );
            } finally {
                setLoading(false);
            }
        };

        if (shipment.id) {
            fetchAudits();
        }
    }, [shipment.id]);

    if (error) {
        return (
            <Card>
                <CardContent className="p-6">
                    <div className="py-8 text-center">
                        <div className="mb-2 text-red-500">
                            Error loading audit history
                        </div>
                        <div className="text-sm text-gray-500">{error}</div>
                    </div>
                </CardContent>
            </Card>
        );
    }

    return <AuditTimeline audits={audits} loading={loading} />;
}
