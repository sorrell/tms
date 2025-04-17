import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Checkbox } from '@/Components/ui/checkbox';
import { Input } from '@/Components/ui/input';
import { Loading } from '@/Components/ui/loading';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/Components/ui/popover';
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
    Accessorial,
    AccessorialType,
    Shipment,
    ShipmentFinancials,
} from '@/types';
import { useForm } from '@inertiajs/react';
import { Check, Package, Pencil, PlusCircle, Trash2, X } from 'lucide-react';
import {
    forwardRef,
    useEffect,
    useImperativeHandle,
    useMemo,
    useRef,
    useState,
} from 'react';

export default function Accessorials({
    shipment,
    shipmentFinancials,
}: {
    shipment: Shipment;
    shipmentFinancials?: ShipmentFinancials;
}) {
    const [accessorialTypes, setAccessorialTypes] = useState<AccessorialType[]>(
        [],
    );
    const [isLoading, setIsLoading] = useState(true);
    const [isEditing, setIsEditing] = useState<boolean>(false);
    const editRowsRef = useRef<{ save: () => void } | null>(null);

    useEffect(() => {
        // Fetch accessorial types
        fetch(route('accounting.accessorial-types.index'), {
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
        })
            .then((response) => response.json())
            .then((data) => {
                setAccessorialTypes(data);
                setIsLoading(false);
            })
            .catch((error) =>
                console.error('Error fetching accessorial types:', error),
            );
    }, [setAccessorialTypes]);

    const handleSave = () => {
        if (editRowsRef.current) {
            editRowsRef.current.save();
            setIsEditing(false);
        }
    };

    const EditRows = forwardRef(
        (
            {
                accessorials,
                accessorial_types,
                shipment,
            }: {
                accessorials: Accessorial[];
                accessorial_types: AccessorialType[];
                shipment: Shipment;
            },
            ref,
        ) => {
            const { data, setData, post } = useForm<{
                accessorials: ShipmentAccessorialData[];
            }>({
                accessorials: accessorials.map((accessorial) => ({
                    id: accessorial.id,
                    rate: accessorial.rate || 0,
                    quantity: accessorial.quantity || 1,
                    total: accessorial.total || 0,
                    accessorial_type_id:
                        accessorial.accessorial_type?.id ||
                        accessorial_types[0]?.id ||
                        0,
                    currency_id: accessorial.currency?.id || 1,
                    customer_id: accessorial.customer?.id,
                    carrier_id: accessorial.carrier?.id,
                    invoice_customer: accessorial.invoice_customer || false,
                    pay_carrier: accessorial.pay_carrier || false,
                })),
            });

            const carrierOptions: { name: string; id: number }[] = [];
            // Add carriers from accessorials, filtering out undefined values
            accessorials.forEach((a) => {
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

            const sortedAccessorialTypes = useMemo(() => {
                return accessorial_types.sort((a, b) =>
                    a.name.localeCompare(b.name),
                );
            }, [accessorial_types]);

            const updateRow = (
                index: number,
                field: keyof ShipmentAccessorialData,
                value: number | string | boolean,
            ) => {
                const newAccessorials = [...data.accessorials];
                newAccessorials[index] = {
                    ...newAccessorials[index],
                    [field]: value,
                };

                // Update total when rate or quantity changes
                if (field === 'rate' || field === 'quantity') {
                    const rate = newAccessorials[index]?.rate || 0;
                    const quantity = newAccessorials[index]?.quantity || 0;
                    newAccessorials[index].total = rate * quantity;
                }

                setData({ accessorials: newAccessorials });
            };

            const addRow = () => {
                // Get values from an existing row if available
                const defaultValues =
                    data.accessorials.length > 0
                        ? data.accessorials[0]
                        : {
                              rate: 0,
                              quantity: 1,
                              total: 0,
                              accessorial_type_id:
                                  accessorial_types[0]?.id || 0,
                              currency_id:
                                  data.accessorials[0]?.currency_id || 1,
                              customer_id:
                                  shipment.customers &&
                                  shipment.customers.length > 0
                                      ? shipment.customers[0].id
                                      : undefined,
                              carrier_id: shipment.carrier?.id,
                              invoice_customer: false,
                              pay_carrier: false,
                          };

                setData({
                    accessorials: [
                        ...data.accessorials,
                        {
                            rate: 0,
                            quantity: 1,
                            total: 0,
                            accessorial_type_id:
                                defaultValues.accessorial_type_id,
                            currency_id: defaultValues.currency_id,
                            customer_id: defaultValues.customer_id,
                            carrier_id: defaultValues.carrier_id,
                            invoice_customer: defaultValues.invoice_customer,
                            pay_carrier: defaultValues.pay_carrier,
                        },
                    ],
                });
            };

            const deleteRow = (index: number) => {
                const newAccessorials = [...data.accessorials];
                newAccessorials.splice(index, 1);
                setData({ accessorials: newAccessorials });
            };

            const save = () => {
                post(
                    route('shipments.financials.accessorials', {
                        shipment: shipment.id,
                    }),
                    {
                        preserveScroll: true,
                        onSuccess: () => {
                            toast({
                                description: 'Accessorials saved!',
                            });
                        },
                        onError: console.error,
                    },
                );
            };

            // Expose the save function to the parent component
            useImperativeHandle(ref, () => ({
                save,
            }));

            return (
                <>
                    <Table className="w-full">
                        <TableHeader>
                            <TableRow className="hidden sm:table-row">
                                <TableHead>Type</TableHead>
                                <TableHead>Customer</TableHead>
                                <TableHead>Carrier</TableHead>
                                <TableHead>Rate</TableHead>
                                <TableHead>Quantity</TableHead>
                                <TableHead>Invoice Customer</TableHead>
                                <TableHead>Pay Carrier</TableHead>
                                <TableHead className="text-right">
                                    Total
                                </TableHead>
                                <TableHead></TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.accessorials.map(
                                (
                                    accessorial: ShipmentAccessorialData,
                                    index: number,
                                ) => (
                                    <TableRow
                                        key={index}
                                        className="grid grid-cols-2 gap-2 sm:table-row sm:gap-0"
                                    >
                                        <TableCell className="w-full sm:w-auto">
                                            <div className="flex flex-col">
                                                <span className="mb-1 text-xs text-muted-foreground sm:hidden">
                                                    Accessorial Type
                                                </span>
                                                <Select
                                                    value={
                                                        accessorial.accessorial_type_id?.toString() ||
                                                        ''
                                                    }
                                                    onValueChange={(val) =>
                                                        updateRow(
                                                            index,
                                                            'accessorial_type_id',
                                                            parseInt(val),
                                                        )
                                                    }
                                                >
                                                    <SelectTrigger className="w-full">
                                                        <SelectValue placeholder="Select accessorial type" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        {sortedAccessorialTypes.map(
                                                            (type) => (
                                                                <SelectItem
                                                                    key={
                                                                        type.id
                                                                    }
                                                                    value={
                                                                        type.id?.toString() ||
                                                                        ''
                                                                    }
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
                                                    Customer
                                                </span>
                                                <Select
                                                    value={accessorial.customer_id?.toString()}
                                                    onValueChange={(val) =>
                                                        updateRow(
                                                            index,
                                                            'customer_id',
                                                            parseInt(val),
                                                        )
                                                    }
                                                >
                                                    <SelectTrigger className="w-full max-w-[100px] truncate">
                                                        <SelectValue placeholder="Select customer" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        {shipment.customers.map(
                                                            (c) => (
                                                                <SelectItem
                                                                    key={c.id}
                                                                    value={c.id?.toString()}
                                                                >
                                                                    {c.name}
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
                                                    Carrier
                                                </span>
                                                <Select
                                                    value={accessorial.carrier_id?.toString()}
                                                    onValueChange={(val) =>
                                                        updateRow(
                                                            index,
                                                            'carrier_id',
                                                            parseInt(val),
                                                        )
                                                    }
                                                >
                                                    <SelectTrigger className="w-full max-w-[100px] truncate">
                                                        <SelectValue placeholder="Select carrier" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        {carrierOptions.map(
                                                            (c) => (
                                                                <SelectItem
                                                                    key={c.id}
                                                                    value={c.id.toString()}
                                                                >
                                                                    {c.name}
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
                                                        {accessorial.currency_symbol ||
                                                            '$'}
                                                    </span>
                                                    <Input
                                                        type="number"
                                                        value={accessorial.rate}
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
                                                    value={accessorial.quantity}
                                                    onChange={(e) =>
                                                        updateRow(
                                                            index,
                                                            'quantity',
                                                            parseInt(
                                                                e.target.value,
                                                            ),
                                                        )
                                                    }
                                                    className="w-full rounded border p-1"
                                                />
                                            </div>
                                        </TableCell>
                                        <TableCell className="w-full sm:w-auto">
                                            <div className="flex flex-col">
                                                <span className="mb-1 text-xs text-muted-foreground sm:hidden">
                                                    Invoice Customer
                                                </span>
                                                <Checkbox
                                                    checked={
                                                        accessorial.invoice_customer
                                                    }
                                                    onCheckedChange={(
                                                        checked,
                                                    ) =>
                                                        updateRow(
                                                            index,
                                                            'invoice_customer',
                                                            !!checked,
                                                        )
                                                    }
                                                />
                                            </div>
                                        </TableCell>
                                        <TableCell className="w-full sm:w-auto">
                                            <div className="flex flex-col">
                                                <span className="mb-1 text-xs text-muted-foreground sm:hidden">
                                                    Pay Carrier
                                                </span>
                                                <Checkbox
                                                    checked={
                                                        accessorial.pay_carrier
                                                    }
                                                    onCheckedChange={(
                                                        checked,
                                                    ) =>
                                                        updateRow(
                                                            index,
                                                            'pay_carrier',
                                                            !!checked,
                                                        )
                                                    }
                                                />
                                            </div>
                                        </TableCell>
                                        <TableCell className="w-full text-right sm:w-auto">
                                            <div className="flex flex-col items-end">
                                                <span className="mb-1 text-xs text-muted-foreground sm:hidden">
                                                    Total
                                                </span>
                                                {accessorial.currency_symbol ||
                                                    '$'}
                                                {(
                                                    accessorial.total || 0
                                                ).toFixed(2)}
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
                                                    <Trash2 className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ),
                            )}
                            {data.accessorials.length === 0 && (
                                <TableRow>
                                    <TableCell
                                        colSpan={9}
                                        className="text-center text-muted-foreground"
                                    >
                                        No accessorials added
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

    interface ShipmentAccessorialData {
        id?: number;
        organization_id?: number;
        shipment_id?: number;
        customer_id?: number;
        carrier_id?: number;
        invoice_customer?: boolean;
        pay_carrier?: boolean;
        rate?: number;
        quantity?: number;
        total?: number;
        accessorial_type_id?: number;
        currency_id?: number;
        currency_symbol?: string;
        [key: string]: string | number | boolean | undefined;
    }

    return (
        <Card>
            <CardHeader>
                <CardTitle className="flex justify-between gap-2">
                    <span className="">
                        <Package className="mr-2 inline h-5 w-5" />
                        Accessorials
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
                                accessorials={
                                    shipmentFinancials?.accessorials ?? []
                                }
                                accessorial_types={accessorialTypes}
                                shipment={shipment}
                            />
                        ) : (
                            <Table className="w-full">
                                <TableHeader>
                                    <TableRow className="hidden sm:table-row">
                                        <TableHead>Type</TableHead>
                                        <TableHead>Customer</TableHead>
                                        <TableHead>Carrier</TableHead>
                                        <TableHead>Rate & Quantity</TableHead>
                                        <TableHead>Invoice Customer</TableHead>
                                        <TableHead>Pay Carrier</TableHead>
                                        <TableHead className="text-right">
                                            Total
                                        </TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {(
                                        shipmentFinancials?.accessorials ?? []
                                    ).map((accessorial, index) => (
                                        <TableRow
                                            key={index}
                                            className="grid grid-cols-2 gap-2 sm:table-row sm:gap-0"
                                        >
                                            <TableCell className="w-full sm:w-auto">
                                                <div className="flex flex-col">
                                                    <span className="mb-1 text-xs text-muted-foreground sm:hidden">
                                                        Type
                                                    </span>
                                                    {
                                                        accessorial
                                                            .accessorial_type
                                                            ?.name
                                                    }
                                                </div>
                                            </TableCell>
                                            <TableCell className="w-full sm:w-auto">
                                                <div className="flex flex-col">
                                                    <span className="mb-1 text-xs text-muted-foreground sm:hidden">
                                                        Customer
                                                    </span>
                                                    <Popover>
                                                        <PopoverTrigger>
                                                            <span className="inline-block max-w-[150px] cursor-pointer truncate">
                                                                {
                                                                    accessorial
                                                                        .customer
                                                                        ?.name
                                                                }
                                                            </span>
                                                        </PopoverTrigger>
                                                        <PopoverContent className="w-auto border-2 border-foreground p-2 shadow">
                                                            {
                                                                accessorial
                                                                    .customer
                                                                    ?.name
                                                            }
                                                        </PopoverContent>
                                                    </Popover>
                                                </div>
                                            </TableCell>
                                            <TableCell className="w-full sm:w-auto">
                                                <div className="flex flex-col">
                                                    <span className="mb-1 text-xs text-muted-foreground sm:hidden">
                                                        Carrier
                                                    </span>
                                                    <Popover>
                                                        <PopoverTrigger>
                                                            <span className="inline-block max-w-[150px] cursor-pointer truncate">
                                                                {accessorial
                                                                    .carrier
                                                                    ?.name ||
                                                                    '-'}
                                                            </span>
                                                        </PopoverTrigger>
                                                        <PopoverContent className="w-auto border-2 border-foreground p-2 shadow">
                                                            {
                                                                accessorial
                                                                    .carrier
                                                                    ?.name
                                                            }
                                                        </PopoverContent>
                                                    </Popover>
                                                </div>
                                            </TableCell>
                                            <TableCell className="w-full sm:w-auto">
                                                <div className="flex flex-col">
                                                    <span className="mb-1 text-xs text-muted-foreground sm:hidden">
                                                        Rate & Quantity
                                                    </span>
                                                    <span className="text-muted-foreground">
                                                        {accessorial.currency
                                                            ?.symbol || '$'}
                                                        {accessorial.rate?.toFixed(
                                                            2,
                                                        ) || '0.00'}{' '}
                                                        x{' '}
                                                        {accessorial.quantity ||
                                                            0}
                                                    </span>
                                                </div>
                                            </TableCell>
                                            <TableCell className="w-full sm:w-auto">
                                                <div className="flex flex-col">
                                                    <span className="mb-1 text-xs text-muted-foreground sm:hidden">
                                                        Invoice Customer
                                                    </span>
                                                    {accessorial.invoice_customer
                                                        ? 'Yes'
                                                        : 'No'}
                                                </div>
                                            </TableCell>
                                            <TableCell className="w-full sm:w-auto">
                                                <div className="flex flex-col">
                                                    <span className="mb-1 text-xs text-muted-foreground sm:hidden">
                                                        Pay Carrier
                                                    </span>
                                                    {accessorial.pay_carrier
                                                        ? 'Yes'
                                                        : 'No'}
                                                </div>
                                            </TableCell>
                                            <TableCell className="w-full text-right sm:w-auto">
                                                <div className="flex flex-col items-end">
                                                    <span className="mb-1 text-xs text-muted-foreground sm:hidden">
                                                        Total
                                                    </span>
                                                    {accessorial.currency
                                                        ?.symbol || '$'}
                                                    {accessorial.total?.toFixed(
                                                        2,
                                                    ) || '0.00'}
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                    {(shipmentFinancials?.accessorials ?? [])
                                        .length === 0 && (
                                        <TableRow>
                                            <TableCell
                                                colSpan={7}
                                                className="text-center text-muted-foreground"
                                            >
                                                No accessorials added
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
