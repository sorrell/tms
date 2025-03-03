import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { useToast } from '@/hooks/UseToast';
import { Shipment } from '@/types';
import { useForm } from '@inertiajs/react';
import { Check, CheckCircle2, FileText, Pencil, X } from 'lucide-react';
import { useState } from 'react';

export default function ShipmentHeader({ shipment }: { shipment: Shipment }) {
    const [editMode, setEditMode] = useState(false);

    const { toast } = useToast();

    const { patch, setData, data } = useForm({
        shipment_number: shipment.shipment_number,
    });

    const updateShipmentNumber = () => {
        patch(
            route('shipments.updateShipmentNumber', {
                shipment: shipment.id,
            }),
            {
                onSuccess: () => {
                    toast({
                        description: (
                            <>
                                <CheckCircle2
                                    className="mr-2 inline h-4 w-4"
                                    color="green"
                                />
                                Shipment number updated
                            </>
                        ),
                    });
                },
            },
        );
    };

    return (
        <div className="flex flex-wrap items-center justify-between gap-4">
            <div>
                <div className="flex items-center gap-2">
                    <h1 className="text-2xl font-bold">Shipment</h1>
                    {editMode ? (
                        <Input
                            value={data.shipment_number ?? ''}
                            onKeyDown={(e) => {
                                if (e.key === 'Enter') {
                                    e.preventDefault();
                                    setEditMode(false);
                                    updateShipmentNumber();
                                }
                            }}
                            onChange={(e) =>
                                setData('shipment_number', e.target.value)
                            }
                            placeholder="#####"
                            className="text-2xl font-bold"
                        />
                    ) : (
                        <h1 className="text-2xl font-bold">
                            {data.shipment_number ?? `id:${shipment.id}`}
                        </h1>
                    )}
                    {editMode ? (
                        <>
                            <Button
                                variant="ghost"
                                onClick={() => {
                                    setEditMode(false);
                                    updateShipmentNumber();
                                }}
                            >
                                <Check />
                            </Button>
                            <Button
                                variant="ghost"
                                onClick={() => {
                                    setEditMode(false);
                                    setData(
                                        'shipment_number',
                                        shipment.shipment_number,
                                    );
                                }}
                            >
                                <X />
                            </Button>
                        </>
                    ) : (
                        <Button
                            className="inline"
                            variant="ghost"
                            onClick={() => setEditMode(!editMode)}
                        >
                            <Pencil />
                        </Button>
                    )}
                </div>
                <p className="text-muted-foreground">Status: Active</p>
            </div>
            <div className="flex gap-2">
                <Button disabled={true}>
                    <FileText className="mr-2 h-4 w-4" />
                    Generate Ratecon
                </Button>
            </div>
        </div>
    );
}
