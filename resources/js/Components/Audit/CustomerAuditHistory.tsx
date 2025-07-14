import { Customer } from '@/types';
import AuditHistory from './AuditHistory';

interface CustomerAuditHistoryProps {
    customer: Customer;
}

export default function CustomerAuditHistory({
    customer,
}: CustomerAuditHistoryProps) {
    return (
        <AuditHistory
            entityId={customer.id}
            routeName="customers.audit-history"
            showTabs={true}
        />
    );
}
