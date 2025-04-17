import Accessorials from '@/Pages/Shipments/Partials/Accessorials';
import CarrierRates from '@/Pages/Shipments/Partials/CarrierRates';
import { Shipment, ShipmentFinancials } from '@/types';
import { useEffect, useState } from 'react';
import CustomerRates from './CustomerRates';

export default function ShipmentFinancialDetails({
    shipment,
}: {
    shipment: Shipment;
}) {
    const [shipmentFinancials, setShipmentFinancials] =
        useState<ShipmentFinancials>();

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
            );
    }, [shipment]);

    return (
        <>
            <CustomerRates
                shipment={shipment}
                shipmentFinancials={shipmentFinancials}
            />

            <CarrierRates
                shipment={shipment}
                shipmentFinancials={shipmentFinancials}
            />

            <Accessorials
                shipment={shipment}
                shipmentFinancials={shipmentFinancials}
            />
        </>
    );
}
