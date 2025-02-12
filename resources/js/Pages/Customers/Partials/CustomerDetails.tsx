import { Card } from '@/Components/ui/card';
import { Customer } from '@/types';

export default function CustomerDetails({ customer }: { customer?: Customer }) {
    return (
        <Card className="p-4">
            <div className="flex flex-col gap-4">
                <h1 className="text-2xl font-bold">{customer?.name}</h1>
            </div>
        </Card>
    );
}
