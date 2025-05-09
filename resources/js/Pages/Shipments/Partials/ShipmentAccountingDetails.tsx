import { Shipment, ShipmentAccounting } from '@/types';
import { useEffect, useState } from 'react';
import Receivables from './Receivables';
import Payables from './Payables';

export default function ShipmentAccountingDetails({
    shipment,
}: {
    shipment: Shipment;
}) {
    const [shipmentAccounting, setShipmentAccounting] =
        useState<ShipmentAccounting>();

    useEffect(() => {
        fetch(route('shipments.accounting', { shipment: shipment.id }), {
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
        })
            .then((response) => response.json())
            .then((data) => setShipmentAccounting(data))
            .catch((error) =>
                console.error('Error fetching shipment accounting:', error),
            );
    }, [shipment]);

    return (
        <>
            <Receivables
                shipment={shipment}
                shipmentAccounting={shipmentAccounting}
            />
            <Payables
                shipment={shipment}
                shipmentAccounting={shipmentAccounting}
            />

            
        </>
    );
}
