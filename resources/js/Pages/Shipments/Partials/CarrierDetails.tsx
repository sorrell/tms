import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { ResourceSearchSelect } from '@/Components/ui/resource-search-select';
import { Skeleton } from '@/Components/ui/skeleton';
import { useToast } from '@/hooks/use-toast';
import { Shipment } from '@/types';
import { useForm } from '@inertiajs/react';
import { CheckCircle2, Truck } from 'lucide-react';
import { useState } from 'react';

export default function CarrierDetails({ shipment }: { shipment: Shipment }) {
    let [editMode, setEditMode] = useState(false);

    const { toast } = useToast();

    const { patch, setData, data, errors } = useForm({
        carrier_id: shipment.carrier.id,
    });

    const updateShipment = () => {
        patch(
            route('shipments.updateGeneral', {
                shipment: shipment.id,
            }),
            {
                onSuccess: () => {
                    setEditMode(false);
                    toast({
                        description: (
                            <>
                                <CheckCircle2
                                    className="mr-2 inline h-4 w-4"
                                    color="green"
                                />
                                Shipment information updated
                            </>
                        ),
                    });
                },
            },
        );
    };

    return (
        <Card>
            <CardHeader>
                <CardTitle className="flex items-center gap-2">
                    <Truck className="h-5 w-5" />
                    Carrier Details
                </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
                <ResourceSearchSelect
                    className="w-full"
                    searchRoute={route('carriers.search')}
                    onValueChange={(value) => setData({ carrier_id: value })}
                    allowMultiple={false}
                    defaultSelectedItems={data.carrier_id}
                />
                <div className="flex flex-col gap-2">
                    <Skeleton className="h-4 w-3/4" />
                    <Skeleton className="h-4 w-1/2" />
                    <Skeleton className="h-4 w-2/3" />
                </div>
            </CardContent>
        </Card>
    );
}
