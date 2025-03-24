import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { BadgeDollarSign } from 'lucide-react';

export default function ShipmentFinancials({ shipmentId }: { shipmentId: number }) {
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
                <div className='col-span-2'>
                    {/* accessorials */}
                    accessorials
                </div>
            </CardContent>
        </Card>
    );
}
