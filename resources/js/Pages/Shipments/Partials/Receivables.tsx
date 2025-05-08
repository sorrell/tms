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
export default function Receivables({
    shipment,
    shipmentAccounting,
}: {
    shipment: Shipment;
    shipmentAccounting?: ShipmentAccounting;
}) {
    const [rateTypes, setRateTypes] = useState<RateType[]>([]);

    const [isEditing, setIsEditing] = useState<boolean>(false);

    const { data, setData, post, errors, processing } = useForm<{
        receivables: ReceivableFormData[];
    }>({
        receivables: shipmentAccounting?.receivables || [],
    });

    const setDataRef = useRef(setData);

    useEffect(() => {
        setDataRef.current({
            receivables: shipmentAccounting?.receivables || [],
        });
    }, [shipmentAccounting]);


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
                                        onError: (e) => {
                                            console.log(e);
                                            toast({
                                                title: 'Error saving receivables',
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
                            <TableHead>Payer</TableHead>
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
                        {data.receivables.map((receivable, index) =>
                            isEditing ? (
                                <TableRow key={index}>
                                    <TableCell>
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
                                                <span>
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
                                    </TableCell>
                                    <TableCell>
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
                                                <span>{findRateType(receivable.rate_type_id)?.name}</span>
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
                                    </TableCell>
                                    <TableCell>
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
                                    </TableCell>
                                    <TableCell>
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
                                    </TableCell>
                                    <TableCell className="text-right">
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
                                    </TableCell>
                                    <TableCell>
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
                                    <TableCell>{findPayer(receivable.payer_id, receivable.payer_type)?.label}</TableCell>
                                    <TableCell>{findRateType(receivable.rate_type_id)?.name}</TableCell>
                                    <TableCell>{receivable.rate}</TableCell>
                                    <TableCell>{receivable.quantity}</TableCell>
                                    <TableCell className="text-right">{receivable.total}</TableCell>
                                    <TableCell></TableCell>
                                </TableRow>
                            )
                        )}
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    );

}
