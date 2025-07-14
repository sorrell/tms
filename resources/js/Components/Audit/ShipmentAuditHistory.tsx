import { Shipment } from '@/types';
import AuditHistory from './AuditHistory';

interface ShipmentAuditHistoryProps {
    shipment: Shipment;
}

export default function ShipmentAuditHistory({
    shipment,
}: ShipmentAuditHistoryProps) {
    return (
        <AuditHistory
            entityId={shipment.id}
            routeName="shipments.audit-history"
            showTabs={false}
        />
    );
}
