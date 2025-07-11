import { Button } from '@/Components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog';
import { Skeleton } from '@/Components/ui/skeleton';
import { ExternalLink } from 'lucide-react';
import { useEffect, useState } from 'react';

interface LinkedData {
    id: number;
    type: string;
    title: string;
    data: Record<string, unknown>;
    view_url?: string;
}

interface AuditLinkedDataModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    modelType: string;
    modelId: string | number;
}

export default function AuditLinkedDataModal({
    open,
    onOpenChange,
    modelType,
    modelId,
}: AuditLinkedDataModalProps) {
    const [linkedData, setLinkedData] = useState<LinkedData | null>(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        if (open && modelId && modelType) {
            fetchLinkedData();
        }
    }, [open, modelId, modelType]);

    const fetchLinkedData = async () => {
        try {
            setLoading(true);
            setError(null);

            const response = await fetch(
                route('audit.linked-data', {
                    type: modelType,
                    id: modelId,
                }),
            );

            if (!response.ok) {
                throw new Error('Failed to fetch linked data');
            }

            const data = await response.json();
            setLinkedData(data);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'An error occurred');
        } finally {
            setLoading(false);
        }
    };

    const formatFieldName = (key: string): string => {
        return key
            .split('_')
            .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    };

    const formatValue = (value: unknown): string => {
        if (value === null || value === undefined) {
            return '—';
        }
        if (typeof value === 'boolean') {
            return value ? 'Yes' : 'No';
        }
        if (typeof value === 'object') {
            return JSON.stringify(value);
        }
        return String(value);
    };

    const handleViewRecord = () => {
        if (linkedData?.view_url) {
            window.open(linkedData.view_url, '_blank');
        }
    };

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="max-w-2xl">
                <DialogHeader>
                    <DialogTitle className="flex items-center justify-between">
                        <span>
                            {loading
                                ? 'Loading...'
                                : linkedData?.title || 'Linked Data'}
                        </span>
                        <div className="flex items-center gap-2">
                            {linkedData?.view_url && (
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={handleViewRecord}
                                >
                                    <ExternalLink className="mr-2 h-4 w-4" />
                                    View Full Record
                                </Button>
                            )}
                        </div>
                    </DialogTitle>
                </DialogHeader>

                <div className="mt-4">
                    {loading && (
                        <div className="space-y-4">
                            <Skeleton className="h-4 w-3/4" />
                            <Skeleton className="h-4 w-1/2" />
                            <Skeleton className="h-4 w-2/3" />
                            <Skeleton className="h-4 w-1/3" />
                        </div>
                    )}

                    {error && (
                        <div className="rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
                            <div className="font-medium">
                                Error loading data
                            </div>
                            <div className="text-sm">{error}</div>
                        </div>
                    )}

                    {linkedData && !loading && !error && (
                        <div className="space-y-4">
                            <div className="rounded-lg border bg-gray-50 p-4">
                                <div className="mb-2 text-sm font-medium text-gray-600">
                                    {linkedData.type}
                                </div>
                                <div className="text-lg font-semibold">
                                    {linkedData.title}
                                </div>
                            </div>

                            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                {Object.entries(linkedData.data)
                                    .filter(([key]) => key !== 'id')
                                    .map(([key, value]) => (
                                        <div
                                            key={key}
                                            className="rounded-lg border bg-white p-3"
                                        >
                                            <div className="mb-1 text-sm font-medium text-gray-600">
                                                {formatFieldName(key)}
                                            </div>
                                            <div className="text-sm">
                                                {formatValue(value)}
                                            </div>
                                        </div>
                                    ))}
                            </div>
                        </div>
                    )}
                </div>
            </DialogContent>
        </Dialog>
    );
}
