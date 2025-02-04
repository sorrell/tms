import InputError from '@/Components/InputError';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Checkbox } from '@/Components/ui/checkbox';
import { Input } from '@/Components/ui/input';
import { useToast } from '@/hooks/use-toast';
import { cn } from '@/lib/utils';
import { Shipment } from '@/types';
import { useForm } from '@inertiajs/react';
import { Check, CheckCircle2, FileText, Pencil, X } from 'lucide-react';
import { useState } from 'react';

export default function ShipmentGeneral({ shipment }: { shipment: Shipment }) {
    let [editMode, setEditMode] = useState(false);

    const { toast } = useToast();

    const { patch, setData, data, errors } = useForm({
        trailer_temperature: shipment.trailer_temperature,
        trailer_temperature_maximum: shipment.trailer_temperature_maximum,
        trailer_temperature_range: shipment.trailer_temperature_range,
        trailer_type_id: shipment.trailer_type_id,
        trailer_size_id: shipment.trailer_size_id,
        weight: shipment.weight,
        trip_distance: shipment.trip_distance,
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
                <CardTitle className="flex items-center justify-between gap-2">
                    <div className="flex items-center gap-2">
                        <FileText className="h-5 w-5" />
                        General
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
                                            trailer_temperature:
                                                shipment.trailer_temperature,
                                            trailer_temperature_maximum:
                                                shipment.trailer_temperature_maximum,
                                            trailer_temperature_range:
                                                shipment.trailer_temperature_range,
                                            trailer_type_id:
                                                shipment.trailer_type_id,
                                            trailer_size_id:
                                                shipment.trailer_size_id,
                                            weight: shipment.weight,
                                            trip_distance:
                                                shipment.trip_distance,
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
                <div className="grid gap-4 md:grid-cols-2">
                    <div>
                        <label className="text-sm font-medium">
                            Temperature (°F)
                        </label>
                        {editMode ? (
                            <>
                                <Input
                                    type="number"
                                    value={data.trailer_temperature}
                                    onChange={(e) =>
                                        setData(
                                            'trailer_temperature',
                                            Number(e.target.value),
                                        )
                                    }
                                />
                                {errors.trailer_temperature && (
                                    <InputError
                                        message={errors.trailer_temperature}
                                        className="mt-2"
                                    />
                                )}
                            </>
                        ) : (
                            <p>{data.trailer_temperature}°F</p>
                        )}
                    </div>
                    <div
                        className={cn(
                            data.trailer_temperature_range
                                ? 'visible'
                                : 'invisible',
                        )}
                    >
                        <label className="text-sm font-medium">
                            Maximum Temperature (°F)
                        </label>
                        {editMode ? (
                            <>
                                <Input
                                    type="number"
                                    value={data.trailer_temperature_maximum}
                                    onChange={(e) =>
                                        setData(
                                            'trailer_temperature_maximum',
                                            Number(e.target.value),
                                        )
                                    }
                                />
                                {errors.trailer_temperature_maximum && (
                                    <InputError
                                        message={
                                            errors.trailer_temperature_maximum
                                        }
                                        className="mt-2"
                                    />
                                )}
                            </>
                        ) : (
                            <p>{data.trailer_temperature_maximum}°F</p>
                        )}
                    </div>
                    <div>
                        <label className="text-sm font-medium">
                            Temperature Range
                        </label>
                        {editMode ? (
                            <div>
                                <Checkbox
                                    checked={data.trailer_temperature_range}
                                    onCheckedChange={(e) => {
                                        setData(
                                            'trailer_temperature_range',
                                            e ? true : false,
                                        );
                                        if (!e) {
                                            setData(
                                                'trailer_temperature_maximum',
                                                null,
                                            );
                                        }
                                    }}
                                />
                                {errors.trailer_temperature_range && (
                                    <InputError
                                        message={
                                            errors.trailer_temperature_range
                                        }
                                        className="mt-2"
                                    />
                                )}
                            </div>
                        ) : (
                            <p>
                                {data.trailer_temperature_range ? 'Yes' : 'No'}
                            </p>
                        )}
                    </div>

                    {/* TODO - Disabled until we fix trailer type and length */}
                    <div>
                        <label className="text-sm font-medium text-muted-foreground">
                            Trailer Type ID
                        </label>
                        {editMode ? (
                            <>
                                <Input
                                    disabled={true}
                                    type="number"
                                    value={data.trailer_type_id}
                                    onChange={(e) =>
                                        setData(
                                            'trailer_type_id',
                                            Number(e.target.value),
                                        )
                                    }
                                />
                                {errors.trailer_type_id && (
                                    <InputError
                                        message={errors.trailer_type_id}
                                        className="mt-2"
                                    />
                                )}
                            </>
                        ) : (
                            <p>{data.trailer_type_id}</p>
                        )}
                    </div>
                    <div>
                        <label className="text-sm font-medium">
                            Weight (lbs)
                        </label>
                        {editMode ? (
                            <>
                                <Input
                                    type="number"
                                    value={data.weight}
                                    onChange={(e) =>
                                        setData(
                                            'weight',
                                            Number(e.target.value),
                                        )
                                    }
                                />
                                {errors.weight && (
                                    <InputError
                                        message={errors.weight}
                                        className="mt-2"
                                    />
                                )}
                            </>
                        ) : (
                            <p>{data.weight} lbs</p>
                        )}
                    </div>
                    <div>
                        <label className="text-sm font-medium">
                            Trip Distance (miles)
                        </label>
                        {editMode ? (
                            <>
                                <Input
                                    type="number"
                                    value={data.trip_distance}
                                    onChange={(e) =>
                                        setData(
                                            'trip_distance',
                                            Number(e.target.value),
                                        )
                                    }
                                />
                                {errors.trip_distance && (
                                    <InputError
                                        message={errors.trip_distance}
                                        className="mt-2"
                                    />
                                )}
                            </>
                        ) : (
                            <p>{data.trip_distance} miles</p>
                        )}
                    </div>
                </div>
            </CardContent>
        </Card>
    );
}
