import { Customer } from '@/types';

export default function CustomerDetails({ customer }: { customer?: Customer }) {
    return (
        <div className="flex flex-col gap-4">
            <h1 className="text-2xl font-bold">{customer?.name}</h1>
        </div>
    );
}
