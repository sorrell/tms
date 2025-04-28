import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Loading } from '@/Components/ui/loading';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import { toast } from '@/hooks/UseToast';
import {
    CarrierRateType,
    Shipment,
    ShipmentCarrierRate,
    ShipmentFinancials,
} from '@/types';
import { useForm } from '@inertiajs/react';
import { Check, Pencil, PlusCircle, Trash, Truck, X } from 'lucide-react';
import {
    forwardRef,
    useEffect,
    useImperativeHandle,
    useRef,
    useState,
} from 'react';

export default function CarrierRates({
    shipment,
    shipmentFinancials,
}: {
    shipment: Shipment;
    shipmentFinancials?: ShipmentFinancials;
}) {
    const [carrierRateTypes, setCarrierRateTypes] = useState<CarrierRateType[]>(
        [],
    );

    const [isLoading, setIsLoading] = useState(true);
    const [isEditing, setIsEditing] = useState<boolean>(false);
    const editRowsRef = useRef<{ save: () => void } | null>(null);

    useEffect(() => {
        // Fetch carrier rate types
        fetch(route('accounting.carrier-rate-types.index'), {
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
        })
            .then((response) => response.json())
            .then((data) => {
                setCarrierRateTypes(data);
                setIsLoading(false);
            })
            .catch((error) =>
                console.error('Error fetching carrier rate types:', error),
            );
    }, [setCarrierRateTypes]);

    const handleSave = () => {
        if (editRowsRef.current) {
            editRowsRef.current.save();
            setIsEditing(false);
        }
    };

    interface ShipmentCarrierRateData {
        id?: number;
        rate: number;
        quantity: number;
        total: number;
        carrier_id: number;
        carrier_rate_type_id: number;
        currency_id: number;
        currency_symbol: string;
        [key: string]: string | number | boolean | undefined;
    }

    const EditRows = forwardRef(
        (
            {
                rates,
                rate_types,
                shipment,
            }: {
                rates: ShipmentCarrierRate[];
                rate_types: CarrierRateType[];
                shipment: Shipment;
            },
            ref,
        ) => {
            const { data, setData, post } = useForm<{
                rates: ShipmentCarrierRateData[];
            }>({
                rates: rates.map((rate) => ({
                    id: rate.id,
                    rate: rate.rate,
                    quantity: rate.quantity,
                    total: rate.total,
                    carrier_id: rate.carrier.id,
                    carrier_rate_type_id: rate.carrier_rate_type.id,
                    currency_id: rate.currency.id,
                    currency_symbol: rate.currency.symbol,
                })),
            });

            const updateRow = (
                index: number,
                field: keyof ShipmentCarrierRateData,
                value: number | string,
            ) => {
                const newRates = [...data.rates];
                newRates[index] = {
                    ...newRates[index],
                    [field]: value,
                };

                // Update total when rate or quantity changes
                if (field === 'rate' || field === 'quantity') {
                    newRates[index].total =
                        newRates[index].rate * newRates[index].quantity;
                }

                setData({ rates: newRates });
            };

            const addRow = () => {
                // Get values from an existing row if available
                const defaultValues =
                    data.rates.length > 0
                        ? data.rates[0]
                        : {
                              rate: 0,
                              quantity: 1,
                              total: 0,
                              carrier_id: shipment.carrier?.id || 0,
                              carrier_rate_type_id: rate_types[0]?.id || 0,
                              currency_id: data.rates[0]?.currency_id || 1,
                              currency_symbol:
                                  data.rates[0]?.currency_symbol || '$',
                          };

                setData({
                    rates: [
                        ...data.rates,
                        {
                            rate: 0,
                            quantity: 1,
                            total: 0,
                            carrier_id: defaultValues.carrier_id,
                            carrier_rate_type_id:
                                defaultValues.carrier_rate_type_id,
                            currency_id: defaultValues.currency_id,
                            currency_symbol: defaultValues.currency_symbol,
                        },
                    ],
                });
            };

            const deleteRow = (index: number) => {
                const newRates = [...data.rates];
                newRates.splice(index, 1);
                setData({ rates: newRates });
            };

            const save = () => {
                post(
                    route('shipments.financials.carrier-rates', {
                        shipment: shipment.id,
                    }),
                    {
                        preserveScroll: true,
                        onSuccess: () => {
                            toast({
                                description: 'Carrier rates saved!',
                            });
                        },
                        onError: console.error,
                    },
                );
            };

            const carrierOptions: { name: string; id: number }[] = [];
            // Add carriers from rates, filtering out undefined values
            rates.forEach((a) => {
                if (
                    a.carrier &&
                    !carrierOptions.find((v) => v.id == a.carrier?.id)
                ) {
                    carrierOptions.push(a.carrier);
                }
            });
            // Add shipment carrier if it exists
            if (
                shipment.carrier &&
                !carrierOptions.find((v) => v.id == shipment.carrier?.id)
            ) {
                carrierOptions.push(shipment.carrier);
            }

            // Expose the save function to the parent component
            useImperativeHandle(ref, () => ({
                save,
            }));

            return (
                <>
                    <Table className="w-full">
                        <TableHeader>
                            <TableRow className="hidden sm:table-row">
                                <TableHead>Carrier</TableHead>
                                <TableHead>Type</TableHead>
                                <TableHead>Rate</TableHead>
                                <TableHead>Quantity</TableHead>
                                <TableHead className="text-right">
                                    Total
                                </TableHead>
                                <TableHead></TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.rates.map(
                                (
                                    rate: ShipmentCarrierRateData,
                                    index: number,
                                ) => (
                                    <TableRow
                                        key={index}
                                        className="grid grid-cols-2 gap-2 sm:table-row sm:gap-0"
                                    >
                                        <TableCell className="w-full sm:w-auto sm:max-w-[150px]">
                                            <div className="flex flex-col">
                                                <span className="mb-1 text-xs text-muted-foreground sm:hidden">
                                                    Carrier
                                                </span>
                                                <Select
                                                    value={rate.carrier_id.toString()}
                                                    onValueChange={(val) =>
                                                        updateRow(
                                                            index,
                                                            'carrier_id',
                                                            parseInt(val),
                                                        )
                                                    }
                                                >
                                                    <SelectTrigger className="w-full truncate">
                                                        <SelectValue
                                                            placeholder="Select carrier"
                                                            className="truncate"
                                                        />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        {carrierOptions.map(
                                                            (carrier) => (
                                                                <SelectItem
                                                                    key={
                                                                        carrier.id
                                                                    }
                                                                    value={carrier.id.toString()}
                                                                    className="truncate"
                                                                >
                                                                    {
                                                                        carrier.name
                                                                    }
                                                                </SelectItem>
                                                            ),
                                                        )}
                                                    </SelectContent>
                                                </Select>
                                            </div>
                                        </TableCell>
                                        <TableCell className="w-full sm:w-auto">
                                            <div className="flex flex-col">
                                                <span className="mb-1 text-xs text-muted-foreground sm:hidden">
                                                    Rate Type
                                                </span>
                                                <Select
                                                    value={rate.carrier_rate_type_id.toString()}
                                                    onValueChange={(val) =>
                                                        updateRow(
                                                            index,
                                                            'carrier_rate_type_id',
                                                            parseInt(val),
                                                        )
                                                    }
                                                >
                                                    <SelectTrigger className="w-full">
                                                        <SelectValue placeholder="Select rate type" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        {rate_types.map(
                                                            (type) => (
                                                                <SelectItem
                                                                    key={
                                                                        type.id
                                                                    }
                                                                    value={type.id?.toString()}
                                                                >
                                                                    {type.name}
                                                                </SelectItem>
                                                            ),
                                                        )}
                                                    </SelectContent>
                                                </Select>
                                            </div>
                                        </TableCell>
                                        <TableCell className="w-full sm:w-auto">
                                            <div className="flex flex-col">
                                                <span className="mb-1 text-xs text-muted-foreground sm:hidden">
                                                    Rate
                                                </span>
                                                <div className="relative w-full">
                                                    <span className="absolute left-2 top-1/2 -translate-y-1/2 text-muted-foreground">
                                                        {rate.currency_symbol}
                                                    </span>
                                                    <Input
                                                        type="number"
                                                        value={rate.rate}
                                                        onChange={(e) =>
                                                            updateRow(
                                                                index,
                                                                'rate',
                                                                parseFloat(
                                                                    e.target
                                                                        .value,
                                                                ),
                                                            )
                                                        }
                                                        className="w-full rounded border p-1 pl-6"
                                                        step="0.01"
                                                    />
                                                </div>
                                            </div>
                                        </TableCell>
                                        <TableCell className="w-full sm:w-auto">
                                            <div className="flex flex-col">
                                                <span className="mb-1 text-xs text-muted-foreground sm:hidden">
                                                    Quantity
                                                </span>
                                                <Input
                                                    type="number"
                                                    value={rate.quantity}
                                                    onChange={(e) =>
                                                        updateRow(
                                                            index,
                                                            'quantity',
                                                            parseFloat(
                                                                e.target.value,
                                                            ),
                                                        )
                                                    }
                                                    className="w-full rounded border p-1"
                                                />
                                            </div>
                                        </TableCell>
                                        <TableCell className="w-full text-right sm:w-auto">
                                            <div className="flex flex-col items-end">
                                                <span className="mb-1 text-xs text-muted-foreground sm:hidden">
                                                    Total
                                                </span>
                                                {rate.currency_symbol}
                                                {rate.total.toFixed(2)}
                                            </div>
                                        </TableCell>
                                        <TableCell className="w-full sm:w-auto">
                                            <div className="flex flex-col items-center">
                                                <span className="mb-1 text-xs text-muted-foreground sm:hidden">
                                                    Delete
                                                </span>
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    className="text-destructive"
                                                    onClick={() =>
                                                        deleteRow(index)
                                                    }
                                                >
                                                    <Trash className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ),
                            )}

                            {data.rates.length === 0 && (
                                <TableRow>
                                    <TableCell
                                        colSpan={5}
                                        className="text-center text-muted-foreground"
                                    >
                                        No carrier rates added
                                    </TableCell>
                                </TableRow>
                            )}
                        </TableBody>
                    </Table>

                    <div className="mt-4 flex justify-between">
                        <Button variant="default" size="sm" onClick={addRow}>
                            <PlusCircle className="h-4 w-4" /> Add
                        </Button>
                    </div>
                </>
            );
        },
    );

    EditRows.displayName = 'EditRows';

    return (
        <Card>
            <CardHeader>
                <CardTitle className="flex justify-between gap-2">
                    <span className="">
                        <Truck className="mr-2 inline h-5 w-5" />
                        Carrier Rates
                    </span>
                    <span>
                        {isEditing ? (
                            <>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    className="ml-2"
                                    onClick={() => handleSave()}
                                >
                                    <Check className="h-4 w-4" />
                                </Button>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    className="ml-2"
                                    onClick={() => setIsEditing(false)}
                                >
                                    <X className="h-4 w-4" />
                                </Button>
                            </>
                        ) : (
                            <Button
                                variant="ghost"
                                size="icon"
                                className="ml-2"
                                onClick={() => setIsEditing(true)}
                            >
                                <Pencil className="h-4 w-4" />
                            </Button>
                        )}
                    </span>
                </CardTitle>
            </CardHeader>
            <CardContent>
                {isLoading ? (
                    <Loading
                        className="mx-auto h-[200px] w-full"
                        text="Loading..."
                    />
                ) : (
                    <>
                        {isEditing ? (
                            <EditRows
                                ref={editRowsRef}
                                rates={
                                    shipmentFinancials?.shipment_carrier_rates ??
                                    []
                                }
                                rate_types={carrierRateTypes}
                                shipment={shipment}
                            />
                        ) : (
                            <Table className="w-full">
                                <TableHeader>
                                    <TableRow className="hidden sm:table-row">
                                        <TableHead>Carrier</TableHead>
                                        <TableHead>Type</TableHead>
                                        <TableHead>Rate & Quantity</TableHead>
                                        <TableHead className="text-right">
                                            Total
                                        </TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {(
                                        shipmentFinancials?.shipment_carrier_rates ??
                                        []
                                    ).map((carrierRate, index) => (
                                        <TableRow
                                            key={index}
                                            className="grid grid-cols-2 gap-2 sm:table-row sm:gap-0"
                                        >
                                            <TableCell>
                                                {carrierRate.carrier.name}
                                            </TableCell>
                                            <TableCell>
                                                {
                                                    carrierRate
                                                        .carrier_rate_type.name
                                                }
                                            </TableCell>
                                            <TableCell className="text-muted-foreground">
                                                {carrierRate.currency.symbol}
                                                {carrierRate.rate} x{' '}
                                                {carrierRate.quantity}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                {carrierRate.currency.symbol}
                                                {carrierRate.total.toFixed(2)}
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                    {(
                                        shipmentFinancials?.shipment_carrier_rates ??
                                        []
                                    ).length === 0 && (
                                        <TableRow>
                                            <TableCell
                                                colSpan={7}
                                                className="text-center text-muted-foreground"
                                            >
                                                No carrier rates added
                                            </TableCell>
                                        </TableRow>
                                    )}
                                </TableBody>
                            </Table>
                        )}
                    </>
                )}
            </CardContent>
        </Card>
    );
}
