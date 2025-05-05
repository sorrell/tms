import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Loading } from '@/Components/ui/loading';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import { toast } from '@/hooks/UseToast';
import {
    RateType,
    Shipment,
    Receivable,
    ShipmentAccounting,
} from '@/types';
import { useForm } from '@inertiajs/react';
import { Check, Pencil, PlusCircle, Trash, Users, X } from 'lucide-react';
import {
    forwardRef,
    useEffect,
    useImperativeHandle,
    useRef,
    useState,
} from 'react';

export default function Receivables({
    shipment,
    shipmentAccounting,
}: {
    shipment: Shipment;
    shipmentAccounting?: ShipmentAccounting;
}) {
    const [rateTypes, setRateTypes] = useState<RateType[]>([]);

    const [isLoading, setIsLoading] = useState(true);
    const [isEditing, setIsEditing] = useState<boolean>(false);

    return (
        <Card>
            <CardHeader>
                <CardTitle>Receivables</CardTitle>
            </CardHeader>
            <CardContent>
            </CardContent>
        </Card>
    );

}
