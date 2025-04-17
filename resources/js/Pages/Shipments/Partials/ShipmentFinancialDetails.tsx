import AccessorialsTable from '@/Components/Shipments/AccessorialsTable';
import CarrierRatesTable from '@/Components/Shipments/CarrierRatesTable';
import CustomerRatesTable from '@/Components/Shipments/CustomerRatesTable';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Loading } from '@/Components/ui/loading';
import {
    AccessorialType,
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

    const [accessorialTypes, setAccessorialTypes] = useState<AccessorialType[]>(
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

        // Fetch accessorial types
        fetch(route('accounting.accessorial-types.index'), {
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
        })
            .then((response) => response.json())
            .then((data) => setAccessorialTypes(data))
            .catch((error) =>
                console.error('Error fetching accessorial types:', error),
            );
    }, [setCustomerRateTypes, setCarrierRateTypes, setAccessorialTypes]);

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
                    <Loading
                        className="mx-auto h-[200px] w-full"
                        text="Loading..."
                    />
                ) : (
                    <>
                        <CustomerRatesTable
                            rate_types={customerRateTypes}
                            rates={
                                shipmentFinancials?.shipment_customer_rates ??
                                []
                            }
                            shipment={shipment}
                        />
                        <CarrierRatesTable
                            rate_types={carrierRateTypes}
                            rates={
                                shipmentFinancials?.shipment_carrier_rates ??
                                []
                            }
                            shipment={shipment}
                        />
                        <AccessorialsTable
                            accessorial_types={accessorialTypes}
                            accessorials={
                                shipmentFinancials?.accessorials ?? []
                            }
                            shipment={shipment}
                        />
                    </>
                )}
            </CardContent>
        </Card>
    );
}
