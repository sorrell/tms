import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Skeleton } from '@/Components/ui/skeleton';
import { CarrierRateType, CustomerRateType, Shipment, ShipmentFinancials } from '@/types';
import { BadgeDollarSign } from 'lucide-react';
import { useEffect, useState } from 'react';
import CustomerRatesTable from '@/Components/Shipments/CustomerRatesTable';
import CarrierRatesTable from '@/Components/Shipments/CarrierRatesTable';

export default function ShipmentFinancialDetails({
    shipment,
}: {
    shipment: Shipment;
}) {
    const [shipmentFinancials, setShipmentFinancials] =
        useState<ShipmentFinancials>();

    const [customerRateTypes, setCustomerRateTypes] =
        useState<CustomerRateType[]>([]);

    const [carrierRateTypes, setCarrierRateTypes] =
        useState<CarrierRateType[]>([]);

    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        // Fetch customer rate types
        fetch(route('accounting.customer-rate-types.index'), {
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
        })
            .then((response) => response.json())
            .then((data) => setCustomerRateTypes(data))
            .catch((error) =>
                console.error('Error fetching customer rate types:', error),
            );

        // Fetch carrier rate types
        fetch(route('accounting.carrier-rate-types.index'), {
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
        })
            .then((response) => response.json())
            .then((data) => setCarrierRateTypes(data))
            .catch((error) =>
                console.error('Error fetching carrier rate types:', error),
            );
    }, [setCustomerRateTypes, setCarrierRateTypes]);

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
    }, [shipment.id]);

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
                            <CustomerRatesTable
                                rate_types={customerRateTypes}
                                rates={shipmentFinancials?.shipment_customer_rates ?? []}
                            />
                        </div>
                        <div className='md:border-l-2 border-l-accent-foreground/30 md:pl-4 mt-8 md:mt-0'>
                            <CarrierRatesTable
                                rate_types={carrierRateTypes}
                                rates={shipmentFinancials?.shipment_carrier_rates ?? []}
                            />
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
