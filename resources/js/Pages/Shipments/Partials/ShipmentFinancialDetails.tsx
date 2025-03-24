import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { ShipmentFinancials } from '@/types';
import { BadgeDollarSign } from 'lucide-react';
import { useEffect, useState } from 'react';

export default function ShipmentFinancialDetails({
    shipmentId,
}: {
    shipmentId: number;
}) {
    const [shipmentFinancials, setShipmentFinancials] =
        useState<ShipmentFinancials>();

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
            );
    }, [shipmentId, setShipmentFinancials]);

    return (
        <Card>
            <CardHeader>
                <CardTitle className="flex items-center gap-2">
                    <BadgeDollarSign className="h-5 w-5" />
                    Financials
                </CardTitle>
            </CardHeader>
            <CardContent className="grid columns-2">
                <div>
                    {/* customer rates */}
                    customer rates
                </div>
                <div>
                    {/* carrier rates */}
                    carrier rates
                </div>
                <div className="col-span-2">
                    {/* accessorials */}
                    accessorials
                </div>
            </CardContent>
        </Card>
    );
}
