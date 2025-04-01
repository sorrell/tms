import CarrierRatesTable from '@/Components/Shipments/CarrierRatesTable';
import CustomerRatesTable from '@/Components/Shipments/CustomerRatesTable';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Skeleton } from '@/Components/ui/skeleton';
import {
    CarrierRateType,
    CustomerRateType,
    Shipment,
    ShipmentFinancials,
} from '@/types';
import { BadgeDollarSign } from 'lucide-react';
import { useEffect, useState } from 'react';

export default function ShipmentFinancialDetails({
    shipment,
}: {
    shipment: Shipment;
}) {
    const [shipmentFinancials, setShipmentFinancials] =
        useState<ShipmentFinancials>();

    const [customerRateTypes, setCustomerRateTypes] = useState<
        CustomerRateType[]
    >([]);

    const [carrierRateTypes, setCarrierRateTypes] = useState<CarrierRateType[]>(
        [],
    );

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
            )
            .finally(() => setIsLoading(false));
    }, [shipment]);

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
                        <Skeleton className="h-[64px] w-full" />
                    </>
                ) : (
                    <>
                        <div className="md:pr-4">
                            <CustomerRatesTable
                                rate_types={customerRateTypes}
                                rates={
                                    shipmentFinancials?.shipment_customer_rates ??
                                    []
                                }
                                shipment={shipment}
                            />
                        </div>
                        <div className="mt-8 border-l-accent-foreground/30 md:mt-0 md:border-l-2 md:pl-4">
                            <CarrierRatesTable
                                rate_types={carrierRateTypes}
                                rates={
                                    shipmentFinancials?.shipment_carrier_rates ??
                                    []
                                }
                                shipment={shipment}
                            />
                        </div>
                        <div className="col-span-1 md:col-span-2">
                            <div className="mt-4 flex items-center justify-center rounded-md border border-dashed p-4">
                                <p className="text-muted-foreground">
                                    Accessorials - Coming Soon
                                </p>
                            </div>
                        </div>
                    </>
                )}
            </CardContent>
        </Card>
    );
}
