import Notes from '@/Components/Notes';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Notable } from '@/types/enums';
import { FilePenIcon } from 'lucide-react';

export default function ShipmentNotes({ shipmentId }: { shipmentId: number }) {
    return (
        <Card>
            <CardHeader>
                <CardTitle className="flex items-center gap-2">
                    <FilePenIcon className="h-5 w-5" />
                    Notes
                </CardTitle>
            </CardHeader>
            <CardContent className="">
                <Notes notableType={Notable.Shipment} notableId={shipmentId} />
            </CardContent>
        </Card>
    );
}
