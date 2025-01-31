import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Label } from '@/Components/ui/label';
import { ResourceSearchSelect } from '@/Components/ui/resource-search-select';
import { Skeleton } from '@/Components/ui/skeleton';
import { useToast } from '@/hooks/use-toast';
import { Shipment } from '@/types';
import { useForm, usePage } from '@inertiajs/react';
import { Box, Check, CheckCircle2, Pencil, Truck, X } from 'lucide-react';
import { useState } from 'react';


export default function ShipperDetails({ shipment }: { shipment: Shipment }) {
    let [editMode, setEditMode] = useState(false);

    const { toast } = useToast();

    const { patch, setData, data, errors } = useForm({
        shipper_ids: shipment.shippers.map((shipper) => shipper.id),
    });

    const updateShipment = () => {
        patch(
            route('shipments.updateShippers', {
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
                                Shippers updated
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
                        <Box className="h-5 w-5" />
                        Shippers
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
                                        setData('shipper_ids', shipment.shippers.map((shipper) => shipper.id));
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
                    <label className="text-sm font-medium">Shippers</label>
                    {editMode ? (
                        <ResourceSearchSelect
                            className="w-full"
                            searchRoute={route('shippers.search')}
                            onValueChange={(value) => setData({ shipper_ids: value })}
                            allowMultiple={true}
                            defaultSelectedItems={data.shipper_ids}
                            allowUnselect={false}
                        />
                    ) : (
                        <div className="flex flex-wrap gap-2">
                            {shipment.shippers.map((shipper) => (
                                <Badge variant="secondary" key={shipper.id}>{shipper.name}</Badge>
                            ))}
                        </div>
                    )}
                </div>
            </CardContent>
        </Card>
    );
}
