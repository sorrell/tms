import NewCheckCallButton from '@/Components/CheckCalls/NewCheckCallButton';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { ConfirmButton } from '@/Components/ui/confirm-button';
import { Loading } from '@/Components/ui/loading';
import { useEventBus } from '@/hooks/useEventBus';
import { useToast } from '@/hooks/UseToast';
import { CheckCall, Contact, Shipment, ShipmentStop } from '@/types';
import { router } from '@inertiajs/react';
import { format } from 'date-fns';
import { ChevronLeft, ChevronRight, ClipboardCheck, Trash } from 'lucide-react';
import { useCallback, useEffect, useRef, useState } from 'react';

type ShipmentCheckCallsProps = {
    shipment: Shipment;
    stops?: ShipmentStop[];
    carrierContacts?: Contact[];
};

export default function ShipmentCheckCalls({
    shipment,
    stops = [],
    carrierContacts = [],
}: ShipmentCheckCallsProps) {
    const [checkCalls, setCheckCalls] = useState<CheckCall[]>([]);
    const [loading, setLoading] = useState(true);
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
    const [expandedNotes, setExpandedNotes] = useState<number[]>([]);
    const itemsPerPage = 5;
    const { toast } = useToast();
    const { subscribe } = useEventBus();
    const subscribeRef = useRef(subscribe);
    const unsubscribeRef = useRef<(() => void) | null>(null);

    const refreshCheckCalls = useCallback(() => {
        fetch(route('shipments.check-calls.index', { shipment: shipment.id }))
            .then((response) => response.json())
            .then((data) => {
                setCheckCalls(data);
                setTotalPages(Math.ceil(data.length / itemsPerPage));
                setLoading(false);
            });
    }, [shipment.id]);

    useEffect(() => {
        refreshCheckCalls();

        // Subscribe to the new event
        unsubscribeRef.current = subscribeRef.current(
            'check-call-added-' + shipment.id,
            () => {
                refreshCheckCalls();
            },
        );

        // Cleanup on unmount
        return () => {
            if (unsubscribeRef.current) {
                unsubscribeRef.current();
            }
        };
    }, [shipment.id, refreshCheckCalls]);

    const handleDelete = (checkCallId: number) => {
        router.delete(
            route('shipments.check-calls.destroy', {
                shipment: shipment.id,
                checkcall: checkCallId,
            }),
            {
                preserveScroll: true,
                onSuccess: () => {
                    toast({
                        description: 'Check call deleted successfully',
                    });
                    refreshCheckCalls();
                },
                onError: () => {
                    toast({
                        title: 'Error',
                        description: 'Failed to delete check call',
                        variant: 'destructive',
                    });
                },
            },
        );
    };

    const formatDateTime = (dateTime: string | null | undefined) => {
        if (!dateTime) return 'N/A';
        return format(new Date(dateTime), 'MMM d, yyyy h:mm a');
    };

    const paginatedCheckCalls = checkCalls.slice(
        (currentPage - 1) * itemsPerPage,
        currentPage * itemsPerPage,
    );

    const goToNextPage = () => {
        if (currentPage < totalPages) {
            setCurrentPage(currentPage + 1);
        }
    };

    const goToPreviousPage = () => {
        if (currentPage > 1) {
            setCurrentPage(currentPage - 1);
        }
    };

    const toggleNoteExpand = (checkCallId: number) => {
        setExpandedNotes((prev) =>
            prev.includes(checkCallId)
                ? prev.filter((id) => id !== checkCallId)
                : [...prev, checkCallId],
        );
    };

    // Find the current stop from the stops array
    const currentStop = stops.find((s) => s.arrived_at && !s.left_at);

    return (
        <Card>
            <CardHeader className="flex flex-row items-center justify-between">
                <CardTitle className="flex items-center gap-2">
                    <ClipboardCheck className="h-5 w-5" />
                    Check Calls
                </CardTitle>
                <NewCheckCallButton
                    shipment={shipment}
                    carrierId={shipment.carrier?.id}
                    stops={shipment.stops}
                    carrierContacts={carrierContacts}
                />
            </CardHeader>
            <CardContent>
                {loading ? (
                    <Loading
                        className="h-[200px]"
                        text="Loading check calls..."
                    />
                ) : checkCalls.length === 0 ? (
                    <div className="py-8 text-center text-sm text-gray-500">
                        No check calls recorded yet
                    </div>
                ) : (
                    <>
                        <div className="space-y-3">
                            {paginatedCheckCalls.map((checkCall) => (
                                <div
                                    key={checkCall.id}
                                    className="border-t p-2"
                                >
                                    <div className="flex items-center justify-between">
                                        <div className="font-medium">
                                            {checkCall.contact_name ||
                                                'Unknown contact'}
                                            {checkCall.contact_method &&
                                                ` (${checkCall.contact_method})`}
                                            {checkCall.contact_method_detail && (
                                                <span className="ml-1 text-sm text-gray-500">
                                                    -{' '}
                                                    {
                                                        checkCall.contact_method_detail
                                                    }
                                                </span>
                                            )}
                                        </div>
                                        <div className="flex items-center gap-2">
                                            <div className="text-xs text-gray-500">
                                                <div>
                                                    {formatDateTime(
                                                        checkCall.created_at,
                                                    )}
                                                </div>
                                                {checkCall.creator && (
                                                    <div className="">
                                                        by{' '}
                                                        {checkCall.creator.name}
                                                    </div>
                                                )}
                                            </div>
                                            <ConfirmButton
                                                variant="ghost"
                                                size="icon"
                                                className="h-7 w-7 text-destructive"
                                                onConfirm={() =>
                                                    handleDelete(checkCall.id)
                                                }
                                                confirmText="Delete"
                                            >
                                                <Trash className="h-4 w-4" />
                                            </ConfirmButton>
                                        </div>
                                    </div>

                                    <div className="mt-2 flex flex-col gap-x-4 gap-y-1 text-sm">
                                        {checkCall.eta && (
                                            <div className="flex items-center gap-1">
                                                <span className="font-medium">
                                                    ETA:
                                                </span>
                                                {formatDateTime(checkCall.eta)}
                                            </div>
                                        )}

                                        {checkCall.reported_trailer_temp !==
                                            null && (
                                            <div className="flex items-center gap-1">
                                                <span className="font-medium">
                                                    Temp:
                                                </span>
                                                {
                                                    checkCall.reported_trailer_temp
                                                }
                                                °
                                            </div>
                                        )}

                                        {checkCall.is_late && (
                                            <div className="flex items-center gap-1 text-destructive">
                                                <span className="font-medium">
                                                    Truck is late
                                                </span>
                                            </div>
                                        )}

                                        {checkCall.is_truck_empty && (
                                            <div className="flex items-center gap-1">
                                                <span className="font-medium">
                                                    Truck is empty
                                                </span>
                                            </div>
                                        )}

                                        {checkCall.arrived_at && (
                                            <div className="flex items-center gap-1">
                                                <span className="font-medium">
                                                    Arrived:
                                                </span>
                                                {formatDateTime(
                                                    checkCall.arrived_at,
                                                )}
                                            </div>
                                        )}

                                        {checkCall.loaded_unloaded_at && (
                                            <div className="flex items-center gap-1">
                                                <span className="font-medium">
                                                    Loaded/Unloaded:
                                                </span>
                                                {formatDateTime(
                                                    checkCall.loaded_unloaded_at,
                                                )}
                                            </div>
                                        )}

                                        {checkCall.left_at && (
                                            <div className="flex items-center gap-1">
                                                <span className="font-medium">
                                                    Left:
                                                </span>
                                                {formatDateTime(
                                                    checkCall.left_at,
                                                )}
                                            </div>
                                        )}
                                    </div>

                                    {checkCall.note && checkCall.note.note && (
                                        <div className="">
                                            <div className="mt-2 text-sm text-gray-600">
                                                {expandedNotes.includes(
                                                    checkCall.id,
                                                ) ? (
                                                    <>
                                                        {checkCall.note.note}
                                                        <Button
                                                            variant="link"
                                                            className="ml-1 h-auto p-0 text-xs text-primary"
                                                            onClick={() =>
                                                                toggleNoteExpand(
                                                                    checkCall.id,
                                                                )
                                                            }
                                                        >
                                                            Show less
                                                        </Button>
                                                    </>
                                                ) : (
                                                    <>
                                                        {checkCall.note.note
                                                            .length > 100
                                                            ? `${checkCall.note.note.substring(0, 100)}... `
                                                            : checkCall.note
                                                                  .note}
                                                        {checkCall.note.note
                                                            .length > 100 && (
                                                            <Button
                                                                variant="link"
                                                                className="h-auto p-0 text-xs text-primary"
                                                                onClick={() =>
                                                                    toggleNoteExpand(
                                                                        checkCall.id,
                                                                    )
                                                                }
                                                            >
                                                                Show more
                                                            </Button>
                                                        )}
                                                    </>
                                                )}
                                            </div>
                                        </div>
                                    )}
                                </div>
                            ))}
                        </div>

                        {checkCalls.length > itemsPerPage && (
                            <div className="mt-4 flex items-center justify-between">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={goToPreviousPage}
                                    disabled={currentPage === 1}
                                    className="flex h-9 items-center gap-1 px-3 py-1"
                                >
                                    <ChevronLeft className="h-4 w-4" />
                                    <span className="hidden sm:inline">
                                        Previous
                                    </span>
                                </Button>

                                <span className="text-sm text-gray-500">
                                    Page {currentPage} of {totalPages}
                                </span>

                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={goToNextPage}
                                    disabled={currentPage === totalPages}
                                    className="flex h-9 items-center gap-1 px-3 py-1"
                                >
                                    <span className="hidden sm:inline">
                                        Next
                                    </span>
                                    <ChevronRight className="h-4 w-4" />
                                </Button>
                            </div>
                        )}
                    </>
                )}
            </CardContent>
        </Card>
    );
}
