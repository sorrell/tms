import { Card, CardContent } from '@/Components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs';
import { Facility } from '@/types';
import { Clock, Table } from 'lucide-react';
import { useEffect, useState } from 'react';
import AuditTable from './AuditTable';
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

interface FacilityAuditHistoryProps {
    facility: Facility;
}

export default function FacilityAuditHistory({
    facility,
}: FacilityAuditHistoryProps) {
    const [audits, setAudits] = useState<AuditEntry[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [activeTab, setActiveTab] = useState('timeline');

    useEffect(() => {
        const fetchAudits = async () => {
            try {
                setLoading(true);
                const response = await fetch(
                    route('facilities.audit-history', facility.id),
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

        if (facility.id) {
            fetchAudits();
        }
    }, [facility.id]);

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

    return (
        <div className="space-y-6">
            <Tabs
                value={activeTab}
                onValueChange={setActiveTab}
                className="w-full"
            >
                <TabsList className="w-full">
                    <TabsTrigger
                        value="timeline"
                        className="flex items-center gap-2"
                    >
                        <Clock className="h-4 w-4" />
                        Audit Timeline
                    </TabsTrigger>
                    <TabsTrigger
                        value="table"
                        className="flex items-center gap-2"
                    >
                        <Table className="h-4 w-4" />
                        Audit Table
                    </TabsTrigger>
                </TabsList>

                <TabsContent value="timeline" className="mt-6">
                    <AuditTimeline audits={audits} loading={loading} />
                </TabsContent>

                <TabsContent value="table" className="mt-6">
                    <AuditTable audits={audits} loading={loading} />
                </TabsContent>
            </Tabs>
        </div>
    );
}
