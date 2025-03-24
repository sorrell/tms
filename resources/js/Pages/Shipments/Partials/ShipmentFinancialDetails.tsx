import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Skeleton } from '@/Components/ui/skeleton';
import { Shipment, ShipmentFinancials } from '@/types';
import { BadgeDollarSign, Truck, Users } from 'lucide-react';
import { useEffect, useState } from 'react';
import RatesTable from '@/Components/Shipments/RatesTable';

export default function ShipmentFinancialDetails({
    shipment,
}: {
    shipment: Shipment;
}) {
    const [shipmentFinancials, setShipmentFinancials] =
        useState<ShipmentFinancials>();

    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        fetch(route('shipments.financials', { shipment: shipment.id }), {
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
    }, [shipment.id, setShipmentFinancials]);

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
                                <Users className='w-5 h-5 inline mr-2' />
                                Customer
                            </h3>
                            {customerRates && Object.values(customerRates).map((customerGroup) => (
                                <RatesTable
                                    key={customerGroup.customer.id}
                                    rates={customerGroup.rates}
                                    title={customerGroup.customer.name}
                                    total={customerGroup.total}
                                    currency={customerGroup.currency}
                                />
                            ))}
                        </div>
                        <div className='md:border-l-2 border-l-accent-foreground/30 md:pl-4'>
                            <h3 className="text-muted-foreground mb-2">
                                <Truck className='w-5 h-5 inline mr-2' /> Carrier
                            </h3>
                            {carrierRates && Object.values(carrierRates).map((carrierGroup) => (
                                <RatesTable
                                    key={carrierGroup.carrier.id}
                                    rates={carrierGroup.rates}
                                    title={carrierGroup.carrier.name}
                                    total={carrierGroup.total}
                                    currency={carrierGroup.currency}
                                />
                            ))}
                        </div>
                        <div className="col-span-1 md:col-span-2">
                            <div className="mt-4 p-4 border border-dashed rounded-md flex items-center justify-center">
                                <p className="text-muted-foreground">Accessorials - Coming Soon</p>
                            </div>
                        </div>
                    </>
                )}
            </CardContent>
        </Card>
    );
}
