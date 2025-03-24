import Notes from '@/Components/Notes';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Notable } from '@/types/enums';
import { BadgeDollarSign, DollarSign, FilePenIcon } from 'lucide-react';

export default function ShipmentFinancials({ shipmentId }: { shipmentId: number }) {
    return (
        <Card>
            <CardHeader>
                <CardTitle className="flex items-center gap-2">
                    <BadgeDollarSign className="h-5 w-5" />
                    Financials
                </CardTitle>
            </CardHeader>
            <CardContent className="">
                
            </CardContent>
        </Card>
    );
}
