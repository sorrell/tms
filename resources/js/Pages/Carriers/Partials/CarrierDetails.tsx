import { Card } from '@/Components/ui/card';
import { Carrier } from '@/types';

export default function CarrierDetails({ carrier }: { carrier?: Carrier }) {
    return (
        <Card className="p-4">
            <div className="flex flex-col gap-4">
                <h1 className="text-2xl font-bold">{carrier?.name}</h1>
            </div>
        </Card>
    );
}
