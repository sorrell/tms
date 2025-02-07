import Notes from '@/Components/Notes';
import { Card, CardContent } from '@/Components/ui/card';
import { Notable } from '@/types/enums';

export default function ShipmentNotes({ shipmentId }: { shipmentId: number }) {
    return (
        <Card>
            <CardContent className="pt-6">
                <Notes notableType={Notable.Shipment} notableId={shipmentId} />
            </CardContent>
        </Card>
    );
}
