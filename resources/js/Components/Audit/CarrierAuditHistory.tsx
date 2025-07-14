import { Carrier } from '@/types';
import AuditHistory from './AuditHistory';

interface CarrierAuditHistoryProps {
    carrier: Carrier;
}

export default function CarrierAuditHistory({
    carrier,
}: CarrierAuditHistoryProps) {
    return (
        <AuditHistory
            entityId={carrier.id}
            routeName="carriers.audit-history"
            showTabs={true}
        />
    );
}
