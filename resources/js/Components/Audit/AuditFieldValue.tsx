import { Button } from '@/Components/ui/button';
import { ExternalLink } from 'lucide-react';
import { useState } from 'react';
import AuditLinkedDataModal from './AuditLinkedDataModal';

interface AuditFieldValueProps {
    fieldName: string;
    value: unknown;
    className?: string;
}

// Map of field names to their corresponding model types
const FOREIGN_KEY_MAPPINGS: Record<string, string> = {
    'billing_location_id': 'location',
    'physical_location_id': 'location',
    'location_id': 'location',
    'billing_contact_id': 'contact',
    'contact_id': 'contact',
    'user_id': 'user',
    'created_by': 'user',
    'updated_by': 'user',
    'carrier_id': 'carrier',
    'customer_id': 'customer',
};

export default function AuditFieldValue({
    fieldName,
    value,
    className = '',
}: AuditFieldValueProps) {
    const [modalOpen, setModalOpen] = useState(false);

    const formatValue = (value: unknown): string => {
        if (value === null || value === undefined) {
            return 'No value';
        }
        if (typeof value === 'boolean') {
            return value ? 'Yes' : 'No';
        }
        if (typeof value === 'object') {
            return JSON.stringify(value);
        }
        return String(value);
    };

    // Check if this field is a foreign key
    const modelType = FOREIGN_KEY_MAPPINGS[fieldName.toLowerCase()];
    const isForeignKey = modelType && value && String(value).match(/^\d+$/);

    if (isForeignKey) {
        return (
            <>
                <div className={`flex items-center gap-2 ${className}`}>
                    <span>{formatValue(value)}</span>
                    <Button
                        variant="ghost"
                        size="sm"
                        onClick={() => setModalOpen(true)}
                        className="h-6 w-6 p-0 text-blue-600 hover:text-blue-800"
                        title={`View ${modelType} details`}
                    >
                        <ExternalLink className="h-3 w-3" />
                    </Button>
                </div>

                <AuditLinkedDataModal
                    open={modalOpen}
                    onOpenChange={setModalOpen}
                    modelType={modelType}
                    modelId={String(value)}
                />
            </>
        );
    }

    return <span className={className}>{formatValue(value)}</span>;
}