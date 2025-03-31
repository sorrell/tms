import { CarrierRateType, ShipmentCarrierRate } from "@/types";
import { Table, TableBody, TableCell, TableRow } from "@/Components/ui/table";
import { Pencil, Truck } from "lucide-react";
import { Button } from "../ui/button";


interface CarrierRatesTableProps {
    rates: ShipmentCarrierRate[];
    rate_types: CarrierRateType[];
}

interface CarrierRateGroup {
    carrier: { id: number; name: string };
    rates: ShipmentCarrierRate[];
    total: number;
    currency: { id: number; code: string; symbol: string };
}


export default function CarrierRatesTable({ rates, rate_types }: CarrierRatesTableProps) {
    let groupedRates = rates.reduce((acc, rate) => {
        if (!acc[rate.carrier.id]) {
            acc[rate.carrier.id] = {
                carrier: rate.carrier,
                rates: [],
                total: 0,
                currency: rate.currency,
            };
        }
        acc[rate.carrier.id].rates.push(rate);
        acc[rate.carrier.id].total += rate.total;
        return acc;
    }, {} as Record<number, CarrierRateGroup>);

    let groups = Object.values(groupedRates).map(group => (
        <CarrierRateGroup
            key={group.carrier.id}
            carrier={group.carrier}
            rates={group.rates}
            total={group.total}
            currency={group.currency}
        />
    ));

    return (
        <>
            <h3 className="mb-2 flex items-center justify-between">
                <span className="text-muted-foreground">
                    <Truck className='w-5 h-5 inline mr-2' />
                    Carrier Rates
                </span>
                <Button
                    variant="ghost"
                    size="icon"
                    className="ml-2"
                >
                    <Pencil className="h-4 w-4" />
                </Button>
            </h3>
            {groups}
        </>
    );
}


function CarrierRateGroup(rateGroup: CarrierRateGroup) {
    let rates = rateGroup.rates;
    let currency = rateGroup.currency;
    let total = rateGroup.total;
    let title = rateGroup.carrier.name;

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
                                {rate.carrier_rate_type?.name}
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