import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { ConfirmButton } from '@/Components/ui/confirm-button';
import { Input } from '@/Components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger } from '@/Components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import { toast } from '@/hooks/UseToast';
import InputError from '@/Components/InputError';
import {
    RateType,
    Shipment,
    Receivable,
    ShipmentAccounting,
} from '@/types';
import { useForm } from '@inertiajs/react';
import { Check, CirclePlus, DollarSign, Pencil, Trash, Truck, Users, Warehouse, X } from 'lucide-react';
import {
    forwardRef,
    useCallback,
    useEffect,
    useImperativeHandle,
    useRef,
    useState,
} from 'react';

type ReceivableFormData = {
    id?: number;
    payer_id: number;
    payer_type: string;
    rate: number;
    quantity: number;
    total: number;
    rate_type_id: number;
    currency_id: number;
};

// Define a type for the errors object from Inertia
type FormErrors = Record<string, string>;

export default function Receivables({
    shipment,
    shipmentAccounting,
}: {
    shipment: Shipment;
    shipmentAccounting?: ShipmentAccounting;
}) {

    const [isEditing, setIsEditing] = useState<boolean>(false);

    const { data, setData, post, errors: formErrors, processing } = useForm<{
        receivables: ReceivableFormData[];
    }>({
        receivables: shipmentAccounting?.receivables || [],
    });
    
    // Correctly type the errors object
    const errors = formErrors as unknown as FormErrors;

    const setDataRef = useRef(setData);

    useEffect(() => {
        if (!isEditing) {
            setDataRef.current({
                receivables: shipmentAccounting?.receivables || [],
            });
        }        
    }, [shipmentAccounting, isEditing]);

    const findRateType = useCallback((id: number) => {
        return shipmentAccounting?.rate_types.find((rateType) => rateType.id === id);
    }, [shipmentAccounting]);

    const findPayer = useCallback((id: number, type: string) => {
        return shipmentAccounting?.related_entities.find(
            (entity) => entity.id === id && entity.alias_name === type
        );
    }, [shipmentAccounting]);

    const payerUniqueIdForComponent = (id: number, type: string) => {
        return `${type}|${id}`;
    }

    const decodePayerUniqueIdForComponent = (uniqueId: string) => {
        const [type, id] = uniqueId.split('|');
        return { type, id: parseInt(id) };
    }

    const getAliasNameIcon = (aliasName: string ) => {
        if (aliasName == 'customer') {
            return Users;
        }

        if (aliasName == 'carrier') {
            return Truck;
        }

        if (aliasName == 'facility') {
            return Warehouse;
        }

    }

    return (
        <Card>
            <CardHeader>
                <CardTitle className="flex items-center justify-between gap-2">
                    <div className="flex items-center gap-2">
                        <DollarSign className="h-5 w-5" />
                        Receivables

                    </div>

                    <div className="flex gap-2">
                        <Button
                            variant="ghost"
                            onClick={() => {
                                setData({
                                    receivables: [...data.receivables, {
                                        payer_id: 0,
                                        payer_type: 'carrier',
                                        rate: 0,
                                        quantity: 0,
                                        total: 0,
                                        rate_type_id: 0,
                                        currency_id: 1,
                                    }],
                                });
                                setIsEditing(true);
                            }}
                        >
                            <CirclePlus className="h-4 w-4" /> New
                        </Button>
                        {isEditing ? (
                            <>
                                <Button variant="ghost" onClick={() => {
                                    post(route('shipments.accounting.receivables', { shipment: shipment.id }), {
                                        preserveScroll: true,
                                        onSuccess: () => {
                                            setIsEditing(false);
                                            toast({
                                                title: 'Receivables saved',
                                            })
                                        },
                                        onError: () => {
                                            toast({
                                                title: 'Error saving receivables',
                                                description: 'Please check form for errors',
                                                variant: 'destructive'
                                            })
                                        }
                                    });
                                }}>
                                    <Check />
                                </Button>
                                <Button
                                    variant="ghost"
                                    onClick={() => {
                                        setIsEditing(false);
                                        setData({
                                            receivables: shipmentAccounting?.receivables || []
                                        })
                                    }}
                                >
                                    <X />
                                </Button>
                            </>
                        ) : (
                            <Button
                                variant="ghost"
                                onClick={() => setIsEditing(true)}
                            >
                                <Pencil />
                                Edit
                            </Button>
                        )}
                    </div>
                </CardTitle>
            </CardHeader>
            <CardContent>
                <Table className="w-full">
                    <TableHeader>
                        <TableRow className="hidden sm:table-row">
                            <TableHead className="w-[30%]">Payer</TableHead>
                            <TableHead className="w-[20%]">Type</TableHead>
                            <TableHead className="w-[15%]">Rate</TableHead>
                            <TableHead className="w-[12%]">Quantity</TableHead>
                            <TableHead className="w-[18%] text-right">
                                Total
                            </TableHead>
                            <TableHead className="w-[5%]"></TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {data.receivables.length === 0 ? (
                            <TableRow>
                                <TableCell colSpan={6} className="h-24 text-center text-muted-foreground">
                                    No receivables added yet
                                </TableCell>
                            </TableRow>
                        ) : (
                            data.receivables.map((receivable, index) =>
                                isEditing ? (
                                    <TableRow key={index}>
                                        <TableCell className="w-[30%]">
                                            <Select
                                                value={payerUniqueIdForComponent(receivable.payer_id, receivable.payer_type)} 
                                                onValueChange={(value) => {
                                                    const { type, id } = decodePayerUniqueIdForComponent(value);
                                                    setData({
                                                        ...data,
                                                        receivables: data.receivables.map(
                                                            (receivable, receivableIndex) => 
                                                                receivableIndex === index ? 
                                                                    { ...receivable, payer_id: id, payer_type: type } : 
                                                                    receivable
                                                        )
                                                    });
                                                }}
                                            >
                                                <SelectTrigger>
                                                    <span className="truncate">
                                                        {(() => {
                                                            const payer = findPayer(receivable.payer_id, receivable.payer_type);
                                                            const Icon = payer?.alias_name ? getAliasNameIcon(payer.alias_name) : null;
                                                            return (
                                                                <>
                                                                    {Icon && <Icon className="w-4 h-4 inline mr-2" />}
                                                                    {payer?.label}
                                                                </>
                                                            );
                                                        })()}
                                                    </span>
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {shipmentAccounting?.related_entities.map((entity) => (
                                                        <SelectItem
                                                            key={entity.alias_name + entity.id} 
                                                            value={payerUniqueIdForComponent(entity.id, entity.alias_name)}
                                                            >
                                                            {(() => {
                                                                const Icon = getAliasNameIcon(entity.alias_name);
                                                                return Icon ? <Icon className="w-4 h-4 inline mr-2" /> : null;
                                                            })()}
                                                            {entity.label}
                                                        </SelectItem>
                                                    ))}
                                                </SelectContent>
                                            </Select>
                                            <InputError message={errors[`receivables.${index}.payer_id` as keyof typeof errors]} />
                                            <InputError message={errors[`receivables.${index}.payer_type` as keyof typeof errors]} />
                                        </TableCell>
                                        <TableCell className="w-[20%]">
                                            <Select value={receivable.rate_type_id?.toString() || ""} onValueChange={(value) => {
                                                setData({
                                                    ...data,
                                                    receivables: data.receivables.map(
                                                        (r, i) => 
                                                            i === index ? 
                                                                { ...r, rate_type_id: parseInt(value) } : 
                                                                r
                                                    )
                                                });
                                            }}>
                                                <SelectTrigger>
                                                    <span className="truncate">{findRateType(receivable.rate_type_id)?.name}</span>
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {shipmentAccounting?.rate_types.map((rateType) => (
                                                        <SelectItem
                                                            key={rateType.id} 
                                                            value={rateType.id.toString()}
                                                        >
                                                            {rateType.name}
                                                        </SelectItem>
                                                    ))}
                                                </SelectContent>
                                            </Select>
                                            <InputError message={errors[`receivables.${index}.rate_type_id` as keyof typeof errors]} />
                                        </TableCell>
                                        <TableCell className="w-[15%]">
                                            <Input type="number" value={receivable.rate} onChange={(e) => {
                                                setData({
                                                    ...data,
                                                    receivables: data.receivables.map(
                                                        (r, i) => 
                                                            i === index ? 
                                                                { ...r, rate: parseFloat(e.target.value) } : 
                                                                r
                                                    )
                                                });
                                            }} />
                                            <InputError message={errors[`receivables.${index}.rate` as keyof typeof errors]} />
                                        </TableCell>
                                        <TableCell className="w-[12%]">
                                            <Input type="number" value={receivable.quantity} onChange={(e) => {
                                                setData({
                                                    ...data,
                                                    receivables: data.receivables.map(
                                                        (r, i) => 
                                                            i === index ? 
                                                                { ...r, quantity: parseInt(e.target.value) } : 
                                                                r
                                                    )
                                                });
                                            }} />
                                            <InputError message={errors[`receivables.${index}.quantity` as keyof typeof errors]} />
                                        </TableCell>
                                        <TableCell className="w-[18%] text-right">
                                            <Input type="number" value={receivable.total} onChange={(e) => {
                                                setData({
                                                    ...data,
                                                    receivables: data.receivables.map(
                                                        (r, i) => 
                                                            i === index ? 
                                                                { ...r, total: parseFloat(e.target.value) } : 
                                                                r
                                                    )
                                                });
                                            }} />
                                            <InputError message={errors[`receivables.${index}.total` as keyof typeof errors]} />
                                        </TableCell>
                                        <TableCell className="w-[5%]">
                                        <ConfirmButton
                                                variant="ghost"
                                                size="icon"
                                                className="h-7 w-7 text-destructive"
                                                onConfirm={() =>
                                                    setData({
                                                        ...data,
                                                        receivables: data.receivables.filter((_, i) => i !== index)
                                                    })
                                                }
                                                confirmText="Delete"
                                            >
                                                <Trash className="h-4 w-4" />
                                            </ConfirmButton>
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    <TableRow key={index}>
                                        <TableCell className="w-[30%]">
                                            <span className="truncate block">
                                                {findPayer(receivable.payer_id, receivable.payer_type)?.label}
                                            </span>
                                        </TableCell>
                                        <TableCell className="w-[20%]">
                                            <span className="truncate block">
                                                {findRateType(receivable.rate_type_id)?.name}
                                            </span>
                                        </TableCell>
                                        <TableCell className="w-[15%]">{receivable.rate}</TableCell>
                                        <TableCell className="w-[12%]">{receivable.quantity}</TableCell>
                                        <TableCell className="w-[18%] text-right">{receivable.total}</TableCell>
                                        <TableCell className="w-[5%]"></TableCell>
                                    </TableRow>
                                )
                            )
                        )}
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    );

}
