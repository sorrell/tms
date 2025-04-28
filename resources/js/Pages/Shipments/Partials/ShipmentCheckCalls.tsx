import NewCheckCallButton from '@/Components/CheckCalls/NewCheckCallButton';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { ConfirmButton } from '@/Components/ui/confirm-button';
import { Loading } from '@/Components/ui/loading';
import { Button } from '@/Components/ui/button';
import { useEventBus } from '@/hooks/useEventBus';
import { useToast } from '@/hooks/UseToast';
import { CheckCall, Contact, Shipment, ShipmentStop } from '@/types';
import { router } from '@inertiajs/react';
import { format } from 'date-fns';
import { ChevronLeft, ChevronRight, ClipboardCheck, Trash2 } from 'lucide-react';
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
        currentPage * itemsPerPage
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
                        <div className="space-y-4">
                            {paginatedCheckCalls.map((checkCall) => (
                                <div
                                    key={checkCall.id}
                                    className="rounded-lg border p-4"
                                >
                                    <div className="mb-2 flex items-center justify-between">
                                        <div className="font-medium">
                                            {checkCall.contact_name ||
                                                'Unknown contact'}
                                            {checkCall.contact_method &&
                                                ` - ${checkCall.contact_method}`}
                                        </div>
                                        <div className="flex gap-2">
                                            <div className="text-sm text-gray-500">
                                                {formatDateTime(
                                                    checkCall.created_at,
                                                )}
                                            </div>
                                            <ConfirmButton
                                                variant="destructive"
                                                size="icon"
                                                onConfirm={() =>
                                                    handleDelete(checkCall.id)
                                                }
                                                confirmText="Delete"
                                            >
                                                <Trash2 className="h-4 w-4" />
                                            </ConfirmButton>
                                        </div>
                                    </div>

                                    <div className="mt-4 grid grid-cols-1 gap-x-4 gap-y-2 md:grid-cols-2">
                                        {checkCall.eta && (
                                            <div>
                                                <span className="text-sm text-gray-500">
                                                    ETA:
                                                </span>{' '}
                                                <span className="text-sm">
                                                    {formatDateTime(checkCall.eta)}
                                                </span>
                                            </div>
                                        )}

                                        {checkCall.reported_trailer_temp !==
                                            null && (
                                            <div>
                                                <span className="text-sm text-gray-500">
                                                    Trailer Temp:
                                                </span>{' '}
                                                <span className="text-sm">
                                                    {
                                                        checkCall.reported_trailer_temp
                                                    }
                                                    °
                                                </span>
                                            </div>
                                        )}

                                        {checkCall.contact_method_detail && (
                                            <div>
                                                <span className="text-sm text-gray-500">
                                                    Contact Detail:
                                                </span>{' '}
                                                <span className="text-sm">
                                                    {
                                                        checkCall.contact_method_detail
                                                    }
                                                </span>
                                            </div>
                                        )}

                                        {checkCall.arrived_at && (
                                            <div>
                                                <span className="text-sm text-gray-500">
                                                    Arrived:
                                                </span>{' '}
                                                <span className="text-sm">
                                                    {formatDateTime(
                                                        checkCall.arrived_at,
                                                    )}
                                                </span>
                                            </div>
                                        )}

                                        {checkCall.left_at && (
                                            <div>
                                                <span className="text-sm text-gray-500">
                                                    Left:
                                                </span>{' '}
                                                <span className="text-sm">
                                                    {formatDateTime(
                                                        checkCall.left_at,
                                                    )}
                                                </span>
                                            </div>
                                        )}

                                        {checkCall.loaded_unloaded_at && (
                                            <div>
                                                <span className="text-sm text-gray-500">
                                                    Loaded/Unloaded:
                                                </span>{' '}
                                                <span className="text-sm">
                                                    {formatDateTime(
                                                        checkCall.loaded_unloaded_at,
                                                    )}
                                                </span>
                                            </div>
                                        )}
                                    </div>
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
                                    className="flex items-center gap-1 px-3 py-1 h-9"
                                >
                                    <ChevronLeft className="h-4 w-4" />
                                    <span className="hidden sm:inline">Previous</span>
                                </Button>
                                
                                <span className="text-sm text-gray-500">
                                    Page {currentPage} of {totalPages}
                                </span>
                                
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={goToNextPage}
                                    disabled={currentPage === totalPages}
                                    className="flex items-center gap-1 px-3 py-1 h-9"
                                >
                                    <span className="hidden sm:inline">Next</span>
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
