import { Badge } from '@/Components/ui/badge';
import { Card, CardContent } from '@/Components/ui/card';
import { cn } from '@/lib/utils';
import { Calendar, Clock, Edit, Plus, Trash2, User } from 'lucide-react';
import { useState } from 'react';
import AuditFieldValue from './AuditFieldValue';

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

interface AuditTimelineProps {
    audits: AuditEntry[];
    loading?: boolean;
}

export default function AuditTimeline({ audits, loading }: AuditTimelineProps) {
    const [expandedEntries, setExpandedEntries] = useState<Set<number>>(
        new Set(),
    );

    const toggleExpanded = (auditId: number) => {
        const newExpanded = new Set(expandedEntries);
        if (newExpanded.has(auditId)) {
            newExpanded.delete(auditId);
        } else {
            newExpanded.add(auditId);
        }
        setExpandedEntries(newExpanded);
    };

    const getEventIcon = (event: string) => {
        switch (event) {
            case 'created':
                return <Plus className="h-4 w-4" />;
            case 'updated':
                return <Edit className="h-4 w-4" />;
            case 'deleted':
                return <Trash2 className="h-4 w-4" />;
            default:
                return <Edit className="h-4 w-4" />;
        }
    };

    const getEventColor = (event: string) => {
        switch (event) {
            case 'created':
                return 'text-green-600 bg-green-100';
            case 'updated':
                return 'text-blue-600 bg-blue-100';
            case 'deleted':
                return 'text-red-600 bg-red-100';
            default:
                return 'text-gray-600 bg-gray-100';
        }
    };

    const getEventTitle = (event: string) => {
        switch (event) {
            case 'created':
                return 'Created';
            case 'updated':
                return 'Updated';
            case 'deleted':
                return 'Deleted';
            default:
                return 'Modified';
        }
    };

    if (loading) {
        return (
            <div className="space-y-4">
                {[...Array(3)].map((_, i) => (
                    <Card key={i} className="animate-pulse">
                        <CardContent className="p-6">
                            <div className="flex gap-4">
                                <div className="h-10 w-10 rounded-full bg-gray-200"></div>
                                <div className="flex-1 space-y-2">
                                    <div className="h-4 w-1/4 rounded bg-gray-200"></div>
                                    <div className="h-3 w-1/2 rounded bg-gray-200"></div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                ))}
            </div>
        );
    }

    if (audits.length === 0) {
        return (
            <div className="py-8 text-center text-gray-500">
                <Calendar className="mx-auto mb-4 h-12 w-12 opacity-50" />
                <p>No audit history available</p>
            </div>
        );
    }

    return (
        <div className="space-y-4">
            {audits.map((audit, index) => (
                <Card key={audit.id} className="relative">
                    {/* Timeline line */}
                    {index < audits.length - 1 && (
                        <div className="absolute bottom-0 left-8 top-16 w-px bg-gray-200"></div>
                    )}

                    <CardContent className="p-6">
                        <div className="flex gap-4">
                            {/* Event Icon */}
                            <div
                                className={cn(
                                    'flex h-10 w-10 items-center justify-center rounded-full',
                                    getEventColor(audit.event),
                                )}
                            >
                                {getEventIcon(audit.event)}
                            </div>

                            {/* Content */}
                            <div className="min-w-0 flex-1">
                                <div className="mb-2 flex items-center justify-between">
                                    <div className="flex items-center gap-3">
                                        <Badge variant="outline">
                                            {getEventTitle(audit.event)}
                                        </Badge>
                                        {audit.entity_type &&
                                            (audit.entity_type === 'Document' ||
                                                audit.entity_type ===
                                                    'Contact') && (
                                                <Badge
                                                    variant="secondary"
                                                    className="text-xs"
                                                >
                                                    {audit.entity_type}:{' '}
                                                    {audit.entity_name}
                                                </Badge>
                                            )}
                                        {audit.user && (
                                            <div className="flex items-center gap-2 text-sm text-gray-600">
                                                <User className="h-4 w-4" />
                                                <span>{audit.user.name}</span>
                                            </div>
                                        )}
                                    </div>
                                    <div className="flex items-center gap-2 text-sm text-gray-500">
                                        <Clock className="h-4 w-4" />
                                        <span>{audit.created_at_human}</span>
                                    </div>
                                </div>

                                {/* Changes Summary */}
                                {audit.changes.length > 0 && (
                                    <div className="mb-3">
                                        <p className="mb-2 text-sm text-gray-600">
                                            {audit.changes.length === 1
                                                ? `Modified ${audit.changes[0].field.toLowerCase()}`
                                                : `Modified ${audit.changes.length} fields`}
                                        </p>

                                        {/* Quick preview for single change */}
                                        {audit.changes.length === 1 && (
                                            <div className="text-sm">
                                                <span className="text-red-600">
                                                    <AuditFieldValue
                                                        fieldName={
                                                            audit.changes[0]
                                                                .field_name
                                                        }
                                                        value={
                                                            audit.changes[0]
                                                                .old_value
                                                        }
                                                        auditUser={audit.user}
                                                    />
                                                </span>
                                                <span className="mx-2 text-gray-400">
                                                    →
                                                </span>
                                                <span className="text-green-600">
                                                    <AuditFieldValue
                                                        fieldName={
                                                            audit.changes[0]
                                                                .field_name
                                                        }
                                                        value={
                                                            audit.changes[0]
                                                                .new_value
                                                        }
                                                        auditUser={audit.user}
                                                    />
                                                </span>
                                            </div>
                                        )}
                                    </div>
                                )}

                                {/* Expand/Collapse for details */}
                                {audit.changes.length > 0 && (
                                    <button
                                        onClick={() => toggleExpanded(audit.id)}
                                        className="text-sm text-blue-600 hover:text-blue-800"
                                    >
                                        {expandedEntries.has(audit.id)
                                            ? 'Hide details'
                                            : `Show ${audit.changes.length > 1 ? 'all changes' : 'details'}`}
                                    </button>
                                )}

                                {/* Expanded Details */}
                                {expandedEntries.has(audit.id) && (
                                    <div className="mt-4 space-y-3 border-t pt-4">
                                        {audit.changes.map(
                                            (change, changeIndex) => (
                                                <div
                                                    key={changeIndex}
                                                    className="rounded-lg bg-gray-50 p-3"
                                                >
                                                    <div className="mb-2 text-sm font-medium">
                                                        {change.field}
                                                    </div>
                                                    <div className="grid grid-cols-1 gap-3 text-sm md:grid-cols-2">
                                                        <div>
                                                            <div className="mb-1 text-gray-500">
                                                                Previous
                                                            </div>
                                                            <div className="rounded border border-red-200 bg-red-50 p-2 text-red-800">
                                                                <AuditFieldValue
                                                                    fieldName={
                                                                        change.field_name
                                                                    }
                                                                    value={
                                                                        change.old_value
                                                                    }
                                                                    auditUser={
                                                                        audit.user
                                                                    }
                                                                />
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div className="mb-1 text-gray-500">
                                                                New
                                                            </div>
                                                            <div className="rounded border border-green-200 bg-green-50 p-2 text-green-800">
                                                                <AuditFieldValue
                                                                    fieldName={
                                                                        change.field_name
                                                                    }
                                                                    value={
                                                                        change.new_value
                                                                    }
                                                                    auditUser={
                                                                        audit.user
                                                                    }
                                                                />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            ),
                                        )}

                                        {/* Additional metadata */}
                                        <div className="space-y-1 text-xs text-gray-500">
                                            <div>
                                                Time:{' '}
                                                {new Date(
                                                    audit.created_at,
                                                ).toLocaleString()}
                                            </div>
                                            {audit.ip_address && (
                                                <div>
                                                    IP: {audit.ip_address}
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>
                    </CardContent>
                </Card>
            ))}
        </div>
    );
}
