import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Label } from '@/Components/ui/label';
import { ResourceSearchSelect } from '@/Components/ui/resource-search-select';
import { Skeleton } from '@/Components/ui/skeleton';
import { useToast } from '@/hooks/use-toast';
import { Shipment } from '@/types';
import { useForm, usePage } from '@inertiajs/react';
import { Check, CheckCircle2, Pencil, Truck, X } from 'lucide-react';
import { useState } from 'react';


export default function CarrierDetails({ shipment }: { shipment: Shipment }) {
    let [editMode, setEditMode] = useState(false);

    const { toast } = useToast();

    const { patch, setData, data, errors } = useForm({
        carrier_id: shipment.carrier.id,
    });

    const updateShipment = () => {
        patch(
            route('shipments.updateCarrierDetails', {
                shipment: shipment.id,
            }),
            {
                onSuccess: (e) => {
                    setEditMode(false);
                    toast({
                        description: (
                            <>
                                <CheckCircle2
                                    className="mr-2 inline h-4 w-4"
                                    color="green"
                                />
                                Carrier details updated
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
                <CardTitle className="flex items-center justify-between gap-2">
                    <div className="flex items-center gap-2">
                        <Truck className="h-5 w-5" />
                        Carrier Details
                    </div>
                    {editMode ? (
                        <>
                            <div className="flex gap-2">
                                <Button
                                    variant="ghost"
                                    onClick={updateShipment}
                                >
                                    <Check />
                                </Button>
                                <Button
                                    variant="ghost"
                                    onClick={() => {
                                        setEditMode(false);
                                        setData({
                                            carrier_id: shipment.carrier.id,
                                        });
                                    }}
                                >
                                    <X />
                                </Button>
                            </div>
                        </>
                    ) : (
                        <Button
                            variant="ghost"
                            onClick={() => setEditMode(true)}
                        >
                            <Pencil />
                        </Button>
                    )}
                </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
                <div className="flex flex-col gap-2">
                    <label className="text-sm font-medium">Carrier</label>
                    {editMode ? (
                        <ResourceSearchSelect
                            className="w-full"
                            searchRoute={route('carriers.search')}
                            onValueChange={(value) => setData({ carrier_id: value })}
                            allowMultiple={false}
                            defaultSelectedItems={data.carrier_id}
                            allowUnselect={false}
                        />
                    ) : (
                        <p>{shipment.carrier.name}</p>
                    )}
                </div>
                <div className="flex flex-col gap-2">
                    <Skeleton className="h-4 w-3/4" />
                    <Skeleton className="h-4 w-1/2" />
                    <Skeleton className="h-4 w-2/3" />
                </div>
            </CardContent>
        </Card>
    );
}
