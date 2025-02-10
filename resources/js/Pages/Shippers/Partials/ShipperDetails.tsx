import { Card } from '@/Components/ui/card';
import { Shipper } from '@/types';

export default function ShipperDetails({ shipper }: { shipper?: Shipper }) {
    return (
        <Card className="p-4">
            <div className="flex flex-col gap-4">
                <h1 className="text-2xl font-bold">{shipper?.name}</h1>
            </div>
        </Card>
    );
}
