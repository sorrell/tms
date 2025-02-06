import InputError from '@/Components/InputError';
import { ResourceSearchSelect } from '@/Components/ResourceSearchSelect';
import { Avatar, AvatarFallback } from '@/Components/ui/avatar';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import { Textarea } from '@/Components/ui/textarea';
import { useToast } from '@/hooks/UseToast';
import { ShipmentStop } from '@/types';
import { StopType } from '@/types/enums';
import { useForm } from '@inertiajs/react';
import {
    ArrowDown,
    ArrowUp,
    Check,
    CheckCircle2,
    MapPin,
    Pencil,
    Trash,
    X,
} from 'lucide-react';
import { useState } from 'react';

type FormErrors = {
    [key: `stops.${number}.${string}`]: string;
};

export default function ShipmentStopsList({
    shipmentId,
    stops,
}: {
    shipmentId: number;
    stops: ShipmentStop[];
}) {
    const [editMode, setEditMode] = useState(false);
    const { toast } = useToast();

    const updateStops = () => {
        patch(
            route('shipments.updateStops', {
                shipment: shipmentId,
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
                                Stops updated successfully
                            </>
                        ),
                    });
                },
            },
        );
    };

    const getSavedStops = () => {
        return stops.map((stop) => ({
            ...stop,
            eta: stop.eta ? new Date(stop.eta).toISOString().slice(0, 16) : '',
            facility_id: stop.facility?.id,
            appointment_type: stop.appointment_type,
            appointment_at: stop.appointment_at
                ? new Date(stop.appointment_at).toISOString().slice(0, 16)
                : '',
            appointment_end_at: stop.appointment_end_at
                ? new Date(stop.appointment_end_at).toISOString().slice(0, 16)
                : '',
            arrived_at: stop.arrived_at
                ? new Date(stop.arrived_at).toISOString().slice(0, 16)
                : '',
            loaded_unloaded_at: stop.loaded_unloaded_at
                ? new Date(stop.loaded_unloaded_at).toISOString().slice(0, 16)
                : '',
            left_at: stop.left_at
                ? new Date(stop.left_at).toISOString().slice(0, 16)
                : '',
        }));
    };

    const { patch, setData, data, errors } = useForm<{ stops: ShipmentStop[] }>(
        {
            stops: getSavedStops(),
        },
    );

    const formErrors = errors as FormErrors;

    return (
        <Card>
            <CardHeader>
                <CardTitle className="flex items-center justify-between gap-2">
                    <div className="flex items-center gap-2">
                        <MapPin className="h-5 w-5" />
                        Stops
                        {editMode && (
                            <Button
                                onClick={() => {
                                    const updatedStops = [...data.stops];
                                    updatedStops.push({
                                        id: null,
                                        shipment_id: shipmentId,
                                        stop_type: StopType.Delivery,
                                        stop_number: data.stops.length + 1,
                                        facility_id: undefined,
                                        eta: '',
                                        appointment_type: '',
                                        appointment_at: '',
                                        appointment_end_at: '',
                                        arrived_at: '',
                                        loaded_unloaded_at: '',
                                        left_at: '',
                                        special_instructions: '',
                                        reference_numbers: '',
                                    });
                                    setData('stops', updatedStops);
                                }}
                            >
                                Add Stop
                            </Button>
                        )}
                    </div>
                    {editMode ? (
                        <div className="flex gap-2">
                            <Button variant="ghost" onClick={updateStops}>
                                <Check />
                            </Button>
                            <Button
                                variant="ghost"
                                onClick={() => {
                                    setEditMode(false);
                                    setData('stops', getSavedStops());
                                }}
                            >
                                <X />
                            </Button>
                        </div>
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
            <CardContent className="space-y-8">
                {data.stops.map((stop, index) => {
                    const previousStop =
                        index > 0 ? data.stops[index - 1] : null;
                    const shouldHideFields =
                        previousStop && !previousStop.left_at;

                    return (
                        <div
                            className="flex items-center justify-between space-x-2"
                            key={'stops-div-' + index}
                        >
                            {editMode && (
                                <div className="flex h-full flex-shrink-0 flex-col justify-between gap-2">
                                    <Button
                                        variant="secondary"
                                        size="icon"
                                        onClick={() => {
                                            const updatedStops = [
                                                ...data.stops,
                                            ];
                                            const temp = updatedStops[index];
                                            updatedStops[index] =
                                                updatedStops[index - 1];
                                            updatedStops[index - 1] = temp;
                                            updatedStops.forEach((stop, i) => {
                                                stop.stop_number = i + 1;
                                            });
                                            setData('stops', updatedStops);
                                        }}
                                        disabled={index === 0}
                                    >
                                        <ArrowUp className="h-4 w-4" />
                                    </Button>
                                    <span className="text-center text-sm font-bold">
                                        {stop.stop_number}
                                    </span>
                                    <Button
                                        variant="secondary"
                                        size="icon"
                                        onClick={() => {
                                            const updatedStops = [
                                                ...data.stops,
                                            ];
                                            const temp = updatedStops[index];
                                            updatedStops[index] =
                                                updatedStops[index + 1];
                                            updatedStops[index + 1] = temp;
                                            updatedStops.forEach((stop, i) => {
                                                stop.stop_number = i + 1;
                                            });
                                            setData('stops', updatedStops);
                                        }}
                                        disabled={
                                            index === data.stops.length - 1
                                        }
                                    >
                                        <ArrowDown className="h-4 w-4" />
                                    </Button>
                                </div>
                            )}
                            <div className="flex min-w-0 flex-grow flex-col border-l-2 border-primary pl-4">
                                <div className="flex flex-wrap gap-4 md:grid md:grid-cols-2">
                                    <div>
                                        {editMode ? (
                                            <>
                                                <label className="text-sm font-medium">
                                                    Stop Type
                                                </label>
                                                <Select
                                                    value={stop.stop_type}
                                                    onValueChange={(value) => {
                                                        const updatedStops = [
                                                            ...data.stops,
                                                        ];
                                                        updatedStops[index] = {
                                                            ...updatedStops[
                                                                index
                                                            ],
                                                            stop_type: value,
                                                        };
                                                        setData(
                                                            'stops',
                                                            updatedStops,
                                                        );
                                                    }}
                                                >
                                                    <SelectTrigger>
                                                        <SelectValue />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem
                                                            value={
                                                                StopType.Pickup
                                                            }
                                                        >
                                                            Pickup
                                                        </SelectItem>
                                                        <SelectItem
                                                            value={
                                                                StopType.Delivery
                                                            }
                                                        >
                                                            Delivery
                                                        </SelectItem>
                                                    </SelectContent>
                                                </Select>
                                                {formErrors[
                                                    `stops.${index}.type`
                                                ] && (
                                                    <InputError
                                                        message={
                                                            formErrors[
                                                                `stops.${index}.type`
                                                            ]
                                                        }
                                                        className="mt-2"
                                                    />
                                                )}
                                            </>
                                        ) : (
                                            <Avatar>
                                                <AvatarFallback className="bg-primary p-1 text-white">
                                                    {stop.stop_type ===
                                                    StopType.Delivery ? (
                                                        <ArrowDown className="inline h-4 w-4" />
                                                    ) : (
                                                        <ArrowUp className="inline h-4 w-4" />
                                                    )}
                                                    <p className="font-semibold">
                                                        {stop.stop_type
                                                            .charAt(0)
                                                            .toUpperCase()}
                                                        {stop.stop_number}
                                                    </p>
                                                </AvatarFallback>
                                            </Avatar>
                                        )}
                                    </div>
                                    <div>
                                        <div className="flex items-center justify-between">
                                            <label className="text-sm font-medium">
                                                Facility
                                            </label>
                                            {editMode && (
                                                <Button
                                                    disabled={
                                                        data.stops.length <= 2
                                                    }
                                                    variant="destructive"
                                                    className="m-1"
                                                    onClick={() => {
                                                        const updatedStops = [
                                                            ...data.stops,
                                                        ];
                                                        updatedStops.splice(
                                                            index,
                                                            1,
                                                        );
                                                        setData(
                                                            'stops',
                                                            updatedStops,
                                                        );
                                                    }}
                                                >
                                                    <Trash className="h-4 w-4" />
                                                </Button>
                                            )}
                                        </div>

                                        {editMode ? (
                                            <>
                                                <ResourceSearchSelect
                                                    searchRoute={route(
                                                        'facilities.search',
                                                    )}
                                                    allowMultiple={false}
                                                    onValueObjectChange={(
                                                        selected,
                                                    ) => {
                                                        const updatedStops = [
                                                            ...data.stops,
                                                        ];
                                                        updatedStops[index] = {
                                                            ...updatedStops[
                                                                index
                                                            ],
                                                            facility_id:
                                                                selected?.value,
                                                            facility: {
                                                                ...updatedStops[
                                                                    index
                                                                ].facility,
                                                                id: selected?.value,
                                                                name: selected?.label,
                                                            },
                                                        };
                                                        setData(
                                                            'stops',
                                                            updatedStops,
                                                        );
                                                    }}
                                                    defaultSelectedItems={
                                                        stop.facility?.id
                                                    }
                                                    className="md:w-full"
                                                />

                                                {formErrors[
                                                    `stops.${index}.facility.name`
                                                ] && (
                                                    <InputError
                                                        message={
                                                            formErrors[
                                                                `stops.${index}.facility.name`
                                                            ]
                                                        }
                                                        className="mt-2"
                                                    />
                                                )}
                                            </>
                                        ) : (
                                            <p>{stop.facility?.name}</p>
                                        )}
                                    </div>
                                    <div>
                                        <label className="text-sm font-medium text-muted-foreground">
                                            Appointment Type
                                        </label>
                                        {editMode ? (
                                            <>
                                                <Select
                                                    disabled={true}
                                                    value={
                                                        stop.appointment_type ||
                                                        ''
                                                    }
                                                    onValueChange={(value) => {
                                                        const updatedStops = [
                                                            ...data.stops,
                                                        ];
                                                        updatedStops[index] = {
                                                            ...updatedStops[
                                                                index
                                                            ],
                                                            appointment_type:
                                                                value,
                                                        };
                                                        setData(
                                                            'stops',
                                                            updatedStops,
                                                        );
                                                    }}
                                                >
                                                    <SelectTrigger>
                                                        <SelectValue />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem value="firm">
                                                            Firm
                                                        </SelectItem>
                                                        <SelectItem value="first_come">
                                                            First Come First
                                                            Serve
                                                        </SelectItem>
                                                        <SelectItem value="live">
                                                            Live Load/Unload
                                                        </SelectItem>
                                                        <SelectItem value="drop">
                                                            Drop
                                                        </SelectItem>
                                                    </SelectContent>
                                                </Select>
                                                {formErrors[
                                                    `stops.${index}.appointment_type`
                                                ] && (
                                                    <InputError
                                                        message={
                                                            formErrors[
                                                                `stops.${index}.appointment_type`
                                                            ]
                                                        }
                                                        className="mt-2"
                                                    />
                                                )}
                                            </>
                                        ) : (
                                            <p className="capitalize">
                                                {stop.appointment_type ||
                                                    'Not set'}
                                            </p>
                                        )}
                                    </div>
                                    <div>
                                        <label className="text-sm font-medium">
                                            Appointment Time
                                        </label>
                                        {editMode ? (
                                            <>
                                                <Input
                                                    type="datetime-local"
                                                    value={
                                                        stop.appointment_at ||
                                                        ''
                                                    }
                                                    onChange={(e) => {
                                                        const updatedStops = [
                                                            ...data.stops,
                                                        ];
                                                        updatedStops[index] = {
                                                            ...updatedStops[
                                                                index
                                                            ],
                                                            appointment_at:
                                                                e.target.value,
                                                        };
                                                        setData(
                                                            'stops',
                                                            updatedStops,
                                                        );
                                                    }}
                                                />
                                                {formErrors[
                                                    `stops.${index}.appointment_at`
                                                ] && (
                                                    <InputError
                                                        message={
                                                            formErrors[
                                                                `stops.${index}.appointment_at`
                                                            ]
                                                        }
                                                        className="mt-2"
                                                    />
                                                )}
                                            </>
                                        ) : (
                                            <p>
                                                {stop.appointment_at
                                                    ? new Date(
                                                          stop.appointment_at,
                                                      ).toLocaleString()
                                                    : 'Not set'}
                                            </p>
                                        )}
                                    </div>
                                    {!shouldHideFields && (
                                        <>
                                            <div>
                                                <label className="text-sm font-medium">
                                                    ETA
                                                </label>
                                                {editMode ? (
                                                    <>
                                                        <Input
                                                            type="datetime-local"
                                                            value={stop.eta}
                                                            onChange={(e) => {
                                                                const updatedStops =
                                                                    [
                                                                        ...data.stops,
                                                                    ];
                                                                updatedStops[
                                                                    index
                                                                ] = {
                                                                    ...updatedStops[
                                                                        index
                                                                    ],
                                                                    eta: e
                                                                        .target
                                                                        .value,
                                                                };
                                                                setData(
                                                                    'stops',
                                                                    updatedStops,
                                                                );
                                                            }}
                                                        />
                                                        {formErrors[
                                                            `stops.${index}.eta`
                                                        ] && (
                                                            <InputError
                                                                message={
                                                                    formErrors[
                                                                        `stops.${index}.eta`
                                                                    ]
                                                                }
                                                                className="mt-2"
                                                            />
                                                        )}
                                                    </>
                                                ) : (
                                                    <p>
                                                        {stop.eta
                                                            ? new Date(
                                                                  stop.eta,
                                                              ).toLocaleString()
                                                            : 'Not set'}
                                                    </p>
                                                )}
                                            </div>
                                            <div>
                                                <label className="text-sm font-medium">
                                                    Arrived At
                                                </label>
                                                {editMode ? (
                                                    <>
                                                        <Input
                                                            type="datetime-local"
                                                            value={
                                                                stop.arrived_at
                                                            }
                                                            onChange={(e) => {
                                                                const updatedStops =
                                                                    [
                                                                        ...data.stops,
                                                                    ];
                                                                updatedStops[
                                                                    index
                                                                ] = {
                                                                    ...updatedStops[
                                                                        index
                                                                    ],
                                                                    arrived_at:
                                                                        e.target
                                                                            .value,
                                                                };
                                                                setData(
                                                                    'stops',
                                                                    updatedStops,
                                                                );
                                                            }}
                                                        />
                                                        {formErrors[
                                                            `stops.${index}.arrived_at`
                                                        ] && (
                                                            <InputError
                                                                message={
                                                                    formErrors[
                                                                        `stops.${index}.arrived_at`
                                                                    ]
                                                                }
                                                                className="mt-2"
                                                            />
                                                        )}
                                                    </>
                                                ) : (
                                                    <p>
                                                        {stop.arrived_at
                                                            ? new Date(
                                                                  stop.arrived_at,
                                                              ).toLocaleString()
                                                            : 'Not set'}
                                                    </p>
                                                )}
                                            </div>
                                            <div>
                                                <label className="text-sm font-medium">
                                                    {stop.stop_type === 'pickup'
                                                        ? 'Loaded At'
                                                        : 'Unloaded At'}
                                                </label>
                                                {editMode ? (
                                                    <>
                                                        <Input
                                                            type="datetime-local"
                                                            value={
                                                                stop.loaded_unloaded_at
                                                            }
                                                            onChange={(e) => {
                                                                const updatedStops =
                                                                    [
                                                                        ...data.stops,
                                                                    ];
                                                                updatedStops[
                                                                    index
                                                                ] = {
                                                                    ...updatedStops[
                                                                        index
                                                                    ],
                                                                    loaded_unloaded_at:
                                                                        e.target
                                                                            .value,
                                                                };
                                                                setData(
                                                                    'stops',
                                                                    updatedStops,
                                                                );
                                                            }}
                                                        />
                                                        {formErrors[
                                                            `stops.${index}.loaded_unloaded_at`
                                                        ] && (
                                                            <InputError
                                                                message={
                                                                    formErrors[
                                                                        `stops.${index}.loaded_unloaded_at`
                                                                    ]
                                                                }
                                                                className="mt-2"
                                                            />
                                                        )}
                                                    </>
                                                ) : (
                                                    <p>
                                                        {stop.loaded_unloaded_at
                                                            ? new Date(
                                                                  stop.loaded_unloaded_at,
                                                              ).toLocaleString()
                                                            : 'Not set'}
                                                    </p>
                                                )}
                                            </div>
                                            <div>
                                                <label className="text-sm font-medium">
                                                    Left At
                                                </label>
                                                {editMode ? (
                                                    <>
                                                        <Input
                                                            type="datetime-local"
                                                            value={stop.left_at}
                                                            onChange={(e) => {
                                                                const updatedStops =
                                                                    [
                                                                        ...data.stops,
                                                                    ];
                                                                updatedStops[
                                                                    index
                                                                ] = {
                                                                    ...updatedStops[
                                                                        index
                                                                    ],
                                                                    left_at:
                                                                        e.target
                                                                            .value,
                                                                };
                                                                setData(
                                                                    'stops',
                                                                    updatedStops,
                                                                );
                                                            }}
                                                        />
                                                        {formErrors[
                                                            `stops.${index}.left_at`
                                                        ] && (
                                                            <InputError
                                                                message={
                                                                    formErrors[
                                                                        `stops.${index}.left_at`
                                                                    ]
                                                                }
                                                                className="mt-2"
                                                            />
                                                        )}
                                                    </>
                                                ) : (
                                                    <p>
                                                        {stop.left_at
                                                            ? new Date(
                                                                  stop.left_at,
                                                              ).toLocaleString()
                                                            : 'Not set'}
                                                    </p>
                                                )}
                                            </div>
                                        </>
                                    )}

                                    <div className="col-span-2">
                                        <label className="text-sm font-medium">
                                            Special Instructions
                                        </label>
                                        {editMode ? (
                                            <>
                                                <Textarea
                                                    value={
                                                        stop.special_instructions ||
                                                        ''
                                                    }
                                                    onChange={(e) => {
                                                        const updatedStops = [
                                                            ...data.stops,
                                                        ];
                                                        updatedStops[index] = {
                                                            ...updatedStops[
                                                                index
                                                            ],
                                                            special_instructions:
                                                                e.target.value,
                                                        };
                                                        setData(
                                                            'stops',
                                                            updatedStops,
                                                        );
                                                    }}
                                                />
                                                {formErrors[
                                                    `stops.${index}.special_instructions`
                                                ] && (
                                                    <InputError
                                                        message={
                                                            formErrors[
                                                                `stops.${index}.special_instructions`
                                                            ]
                                                        }
                                                        className="mt-2"
                                                    />
                                                )}
                                            </>
                                        ) : (
                                            <p>
                                                {stop.special_instructions ||
                                                    'None'}
                                            </p>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>
                    );
                })}
            </CardContent>
        </Card>
    );
}
