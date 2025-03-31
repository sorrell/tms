import { CustomerRateType, Shipment, ShipmentCustomerRate } from "@/types";
import { Table, TableBody, TableCell, TableRow } from "@/Components/ui/table";
import { Check, Pencil, PlusCircle, Save, Trash2, Users, X } from "lucide-react";
import { Button } from "../ui/button";
import { useState, useRef } from "react";
import { useForm } from "@inertiajs/react";
import { Input } from "../ui/input";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "../ui/select";
import { forwardRef, useImperativeHandle } from "react";


interface CustomerRatesTableProps {
    rates: ShipmentCustomerRate[];
    rate_types: CustomerRateType[];
    shipment: Shipment;
}

/** Use for posting to the API */
interface ShipmentCustomerRateData {
    id?: number;
    rate: number;
    quantity: number;
    total: number;
    customer_id: number;
    customer_rate_type_id: number;
    currency_id: number;
}

interface CustomerRateGroup {
    customer: { id: number; name: string };
    rates: ShipmentCustomerRate[];
    total: number;
    currency: { id: number; code: string; symbol: string };
}


export default function CustomerRatesTable({ rates, rate_types, shipment }: CustomerRatesTableProps) {

    const [isEditing, setIsEditing] = useState<boolean>(false);
    const editRowsRef = useRef<{ save: () => void } | null>(null);

    const handleSave = () => {
        if (editRowsRef.current) {
            editRowsRef.current.save();
            setIsEditing(false);
        }
    };

    let groupedRates = rates.reduce((acc, rate) => {
        if (!acc[rate.customer.id]) {
            acc[rate.customer.id] = {
                customer: rate.customer,
                rates: [],
                total: 0,
                currency: rate.currency,
            };
        }
        acc[rate.customer.id].rates.push(rate);
        acc[rate.customer.id].total += rate.total;
        return acc;
    }, {} as Record<number, CustomerRateGroup>);


    return (
        <>
            <h3 className="mb-2 flex items-center justify-between">
                <span className="text-muted-foreground">
                    <Users className='w-5 h-5 inline mr-2' />
                    Customer Rates
                </span>
                <span>
                    {isEditing ?
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
                        </> :
                        <Button
                            variant="ghost"
                            size="icon"
                            className="ml-2"
                            onClick={() => setIsEditing(true)}
                        >
                            <Pencil className="h-4 w-4" />
                        </Button>
                    }
                </span>
            </h3>


            {isEditing ?
                <EditRows ref={editRowsRef} rates={rates} rate_types={rate_types} shipment={shipment} /> :
                Object.values(groupedRates).map(group => (
                    <CustomerRateGroup
                        key={group.customer.id}
                        customer={group.customer}
                        rates={group.rates}
                        total={group.total}
                        currency={group.currency}
                    />
                ))}
        </>
    );
}

const EditRows = forwardRef(({ rates, rate_types, shipment }: {
    rates: ShipmentCustomerRate[];
    rate_types: CustomerRateType[];
    shipment: Shipment;
}, ref) => {
    const { data, setData, post } = useForm<{ rates: ShipmentCustomerRateData[] }>(
        {
            rates: rates.map(rate => ({
                id: rate.id,
                rate: rate.rate,
                quantity: rate.quantity,
                total: rate.total,
                customer_id: rate.customer.id,
                customer_rate_type_id: rate.customer_rate_type.id,
                currency_id: rate.currency.id
            }))
        }
    );

    const updateRow = (index: number, field: keyof ShipmentCustomerRateData, value: any) => {
        const newRates = [...data.rates];
        newRates[index] = {
            ...newRates[index],
            [field]: value
        };

        // Update total when rate or quantity changes
        if (field === 'rate' || field === 'quantity') {
            newRates[index].total = newRates[index].rate * newRates[index].quantity;
        }

        setData({ rates: newRates });
    };

    const addRow = () => {
        // Get values from an existing row if available
        const defaultValues = data.rates.length > 0 ? data.rates[0] : {
            rate: 0,
            quantity: 1,
            total: 0,
            customer_id: shipment.customers[0]?.id || 0,
            customer_rate_type_id: rate_types[0]?.id || 0,
            currency_id: data.rates[0]?.currency_id || 0
        };

        setData({
            rates: [
                ...data.rates,
                {
                    rate: 0,
                    quantity: 1, 
                    total: 0,
                    customer_id: defaultValues.customer_id,
                    customer_rate_type_id: defaultValues.customer_rate_type_id,
                    currency_id: defaultValues.currency_id
                }
            ]
        });
    };

    const deleteRow = (index: number) => {
        const newRates = [...data.rates];
        newRates.splice(index, 1);
        setData({ rates: newRates });
    };

    const save = () => {
        post(
            route('shipments.financials.customer-rates', { shipment: shipment.id }),
            {
                preserveScroll: true,
                onSuccess: console.log,
                onError: console.error
            }
        );
    };

    // Expose the save function to the parent component
    useImperativeHandle(ref, () => ({
        save
    }));

    return (
        <>
            <Table className="w-full">
                <TableBody>
                    {data.rates.map((rate: ShipmentCustomerRateData, index: number) => (
                        <TableRow key={index} className="grid grid-cols-2 gap-2 sm:table-row sm:gap-0">
                            <TableCell className="w-full sm:w-auto sm:max-w-[150px]">
                                <div className="flex flex-col">
                                    <span className="text-xs text-muted-foreground mb-1 sm:hidden">Customer</span>
                                    <Select
                                        value={rate.customer_id.toString()}
                                        onValueChange={(val) => updateRow(index, 'customer_id', parseInt(val))}
                                    >
                                        <SelectTrigger className="truncate w-full">
                                            <SelectValue placeholder="Select customer" className="truncate" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {shipment.customers.map(customer => (
                                                <SelectItem key={customer.id} value={customer.id.toString()} className="truncate">
                                                    {customer.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>
                            </TableCell>
                            <TableCell className="w-full sm:w-auto">
                                <div className="flex flex-col">
                                    <span className="text-xs text-muted-foreground mb-1 sm:hidden">Rate Type</span>
                                    <Select
                                        value={rate.customer_rate_type_id.toString()}
                                        onValueChange={(val) => updateRow(index, 'customer_rate_type_id', parseInt(val))}
                                    >
                                        <SelectTrigger className="w-full">
                                            <SelectValue placeholder="Select rate type" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {rate_types.map(type => (
                                                <SelectItem key={type.id} value={type.id?.toString()}>
                                                    {type.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>
                            </TableCell>
                            <TableCell className="w-full sm:w-auto">
                                <div className="flex flex-col">
                                    <span className="text-xs text-muted-foreground mb-1 sm:hidden">Rate</span>
                                    <Input
                                        type="number"
                                        value={rate.rate}
                                        onChange={(e) => updateRow(index, 'rate', parseFloat(e.target.value))}
                                        className="w-full p-1 border rounded"
                                        step="0.01"
                                    />
                                </div>
                            </TableCell>
                            <TableCell className="w-full sm:w-auto">
                                <div className="flex flex-col">
                                    <span className="text-xs text-muted-foreground mb-1 sm:hidden">Quantity</span>
                                    <Input
                                        type="number"
                                        value={rate.quantity}
                                        onChange={(e) => updateRow(index, 'quantity', parseInt(e.target.value))}
                                        className="w-full p-1 border rounded"
                                    />
                                </div>
                            </TableCell>
                            <TableCell className="text-right w-full sm:w-auto">
                                <div className="flex flex-col items-end">
                                    <span className="text-xs text-muted-foreground mb-1 sm:hidden">Total</span>
                                    {rate.total.toFixed(2)}
                                </div>
                            </TableCell>
                            <TableCell className="w-full sm:w-auto">
                                <div className="flex flex-col items-center">
                                    <span className="text-xs text-muted-foreground mb-1 sm:hidden">Delete</span>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        className="text-destructive"
                                        onClick={() => deleteRow(index)}
                                    >
                                        <Trash2 className="h-4 w-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>

            <div className="flex justify-between mt-4">
                <Button
                    variant="outline"
                    size="sm"
                    onClick={addRow}
                >
                    <PlusCircle className="h-4 w-4" /> Add
                </Button>
            </div>
        </>
    );
});


function CustomerRateGroup(rateGroup: CustomerRateGroup) {

    let rates = rateGroup.rates;
    let currency = rateGroup.currency;
    let total = rateGroup.total;
    let title = rateGroup.customer.name;

    return (
        <div className="mb-4">
            <div className="flex justify-between items-center mb-2 bg-foreground/5 p-1 rounded">
                <h4 className="font-medium">{title}</h4>
                <span className="text-sm text-muted-foreground">
                    Total: {currency.symbol}{total.toFixed(2)}
                </span>
            </div>
            <Table>
                <TableBody>
                    {rates.map((rate, index) => (
                        <TableRow key={index}>
                            <TableCell>
                                {rate.customer_rate_type?.name}
                            </TableCell>
                            <TableCell className="text-right text-muted-foreground">
                                {rate.currency.symbol}{rate.rate.toFixed(2)} x {rate.quantity}
                            </TableCell>
                            <TableCell className="text-right">
                                {rate.currency.symbol}{rate.total.toFixed(2)}
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
        </div>
    );
}