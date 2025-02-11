import { Card } from '@/Components/ui/card';
import { Facility } from '@/types';

export default function FacilityDetails({ facility }: { facility?: Facility }) {
    return (
        <Card className="p-4">
            <div className="flex flex-col gap-4">
                <h1 className="text-2xl font-bold">{facility?.name}</h1>
            </div>
        </Card>
    );
}
