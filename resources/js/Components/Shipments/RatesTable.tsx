import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';

interface Rate {
    rate: number;
    quantity: number;
    total: number;
    currency: {
        symbol: string;
    };
    customer_rate_type?: {
        name: string;
    };
    carrier_rate_type?: {
        name: string;
    };
}

interface RatesTableProps {
    rates: Rate[];
    title: string;
    total: number;
    currency: {
        symbol: string;
    };
}

export default function RatesTable({ rates, title, total, currency }: RatesTableProps) {
    return (
        <div className="mb-4">
            <div className="flex justify-between items-center mb-2 bg-foreground/5 p-1 rounded">
                <h4 className="font-medium">{title}</h4>
                <span className="text-sm text-muted-foreground">
                    Total: {currency.symbol}{total.toFixed(2)}
                </span>
            </div>
            <Table>
                <TableBody>
                    {rates.map((rate, index) => (
                        <TableRow key={index}>
                            <TableCell>
                                {rate.customer_rate_type?.name || rate.carrier_rate_type?.name}
                            </TableCell>
                            <TableCell className="text-right text-muted-foreground">
                                {rate.currency.symbol}{rate.rate.toFixed(2)} x {rate.quantity}
                            </TableCell>
                            <TableCell className="text-right">
                                {rate.currency.symbol}{rate.total.toFixed(2)}
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
        </div>
    );
} 