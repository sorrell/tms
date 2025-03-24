import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Skeleton } from '@/Components/ui/skeleton';
import { ShipmentFinancials } from '@/types';
import { BadgeDollarSign, Truck, Users } from 'lucide-react';
import { useEffect, useState } from 'react';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';

export default function ShipmentFinancialDetails({
    shipmentId,
}: {
    shipmentId: number;
}) {
    const [shipmentFinancials, setShipmentFinancials] =
        useState<ShipmentFinancials>();

    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        fetch(route('shipments.financials', { shipment: shipmentId }), {
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
        })
            .then((response) => response.json())
            .then((data) => setShipmentFinancials(data))
            .catch((error) =>
                console.error('Error fetching shipment financials:', error),
            ).finally(() => setIsLoading(false));
    }, [shipmentId, setShipmentFinancials]);

    // Group rates by customer
    const customerRates = shipmentFinancials?.shipment_customer_rates.reduce((acc, rate) => {
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
    }, {} as Record<number, {
        customer: { id: number; name: string };
        rates: typeof shipmentFinancials.shipment_customer_rates;
        total: number;
        currency: { id: number; code: string; symbol: string };
    }>);

    // Group rates by carrier
    const carrierRates = shipmentFinancials?.shipment_carrier_rates.reduce((acc, rate) => {
        if (!acc[rate.carrier_id]) {
            acc[rate.carrier_id] = {
                carrier: rate.carrier,
                rates: [],
                total: 0,
                currency: rate.currency,
            };
        }
        acc[rate.carrier_id].rates.push(rate);
        acc[rate.carrier_id].total += rate.total;
        return acc;
    }, {} as Record<number, {
        carrier: { id: number; name: string };
        rates: typeof shipmentFinancials.shipment_carrier_rates;
        total: number;
        currency: { id: number; code: string; symbol: string };
    }>);

    return (
        <Card>
            <CardHeader>
                <CardTitle className="flex items-center gap-2">
                    <BadgeDollarSign className="h-5 w-5" />
                    Financials
                </CardTitle>
            </CardHeader>
            <CardContent className="grid columns-1 md:columns-2">
                {isLoading ? (
                    <>
                        <Skeleton className='w-full h-[64px]' />
                    </>
                ) : (
                    <>
                        <div className="md:pr-4">
                            <h3 className="text-muted-foreground mb-2">
                                <Users className='w-5 h-5 inline' />
                                Customer
                            </h3>
                            {customerRates && Object.values(customerRates).map((customerGroup) => (
                                <div key={customerGroup.customer.id} className="mb-4">
                                    <div className="flex justify-between items-center mb-2 bg-foreground/5 p-1 rounded">
                                        <h4 className="font-medium">{customerGroup.customer.name}</h4>
                                        <span className="text-sm text-muted-foreground">
                                            Total: {customerGroup.currency.symbol}{customerGroup.total.toFixed(2)}
                                        </span>
                                    </div>
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead>Type</TableHead>
                                                <TableHead className="text-right">Rate</TableHead>
                                                <TableHead className="text-right">Total</TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {customerGroup.rates.map((rate) => (
                                                <TableRow key={`${rate.customer_id}-${rate.customer_rate_type_id}`}>
                                                    <TableCell>{rate.customer_rate_type.name}</TableCell>
                                                    <TableCell className="text-right">
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
                            ))}
                        </div>
                        <div className='md:border-l-2 border-l-accent-foreground md:pl-4'>
                            <h3 className="text-muted-foreground mb-2"><Truck className='w-5 h-5 inline' /> Carrier</h3>
                            {carrierRates && Object.values(carrierRates).map((carrierGroup) => (
                                <div key={carrierGroup.carrier.id} className="mb-4">
                                    <div className="flex justify-between items-center mb-2 bg-foreground/5 p-1 rounded">
                                        <h4 className="font-medium">{carrierGroup.carrier.name}</h4>
                                        <span className="text-sm text-muted-foreground">
                                            Total: {carrierGroup.currency.symbol}{carrierGroup.total.toFixed(2)}
                                        </span>
                                    </div>
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead>Type</TableHead>
                                                <TableHead className="text-right">Rate</TableHead>
                                                <TableHead className="text-right">Total</TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {carrierGroup.rates.map((rate) => (
                                                <TableRow key={`${rate.carrier_id}-${rate.carrier_rate_type_id}`}>
                                                    <TableCell>{rate.carrier_rate_type.name}</TableCell>
                                                    <TableCell className="text-right">
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
                            ))}
                        </div>
                        <div className="col-span-1 md:col-span-2">
                            accessorials
                        </div>
                    </>
                )}
            </CardContent>
        </Card>
    );
}
