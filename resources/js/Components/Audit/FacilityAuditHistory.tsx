import { Facility } from '@/types';
import AuditHistory from './AuditHistory';

interface FacilityAuditHistoryProps {
    facility: Facility;
}

export default function FacilityAuditHistory({
    facility,
}: FacilityAuditHistoryProps) {
    return (
        <AuditHistory
            entityId={facility.id}
            routeName="facilities.audit-history"
            showTabs={true}
        />
    );
}
