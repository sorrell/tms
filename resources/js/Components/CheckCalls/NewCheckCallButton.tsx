import { Button } from '@/Components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/Components/ui/dialog';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import { useEventBus } from '@/hooks/useEventBus';
import { useToast } from '@/hooks/UseToast';
import { Contact, ShipmentStop } from '@/types';
import { useForm } from '@inertiajs/react';
import { Clipboard } from 'lucide-react';
import { useState } from 'react';

type NewCheckCallButtonProps = {
    shipmentId: number;
    stopId?: number;
    stops?: ShipmentStop[];
    carrierContacts?: Contact[];
    carrierId?: number;
    buttonText?: string;
    buttonVariant?:
        | 'default'
        | 'destructive'
        | 'outline'
        | 'secondary'
        | 'ghost'
        | 'link';
};

export default function NewCheckCallButton({
    shipmentId,
    stopId,
    stops = [],
    carrierContacts = [],
    carrierId,
    buttonText = 'New Check Call',
    buttonVariant = 'default',
}: NewCheckCallButtonProps) {
    const [open, setOpen] = useState(false);
    const { toast } = useToast();
    const { emit } = useEventBus();

    const contactMethods = [
        { value: 'phone', label: 'Phone' },
        { value: 'email', label: 'Email' },
        { value: 'sms', label: 'SMS' },
        { value: 'app', label: 'App' },
        { value: 'other', label: 'Other' },
    ];

    const { data, setData, post, processing, errors, reset } = useForm({
        stop_id: stopId || '',
        eta: '',
        reported_trailer_temp: '',
        contact_name: '',
        contact_method: '',
        contact_method_detail: '',
        arrived_at: '',
        left_at: '',
        loaded_unloaded_at: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('shipments.check-calls.store', { shipment: shipmentId }), {
            onSuccess: () => {
                toast({
                    description: 'Check call created successfully',
                });
                reset();
                setOpen(false);
                emit('check-call-added-' + shipmentId);
            },
            onError: () => {
                toast({
                    title: 'Error',
                    description: 'Failed to create check call',
                    variant: 'destructive',
                });
            },
        });
    };

    const selectContact = (contact: Contact) => {
        setData({
            ...data,
            contact_name: contact.name,
        });
    };

    if (!carrierId) {
        return <></>;
    }

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button variant={buttonVariant}>
                    <Clipboard className="mr-2 h-4 w-4" />
                    {buttonText}
                </Button>
            </DialogTrigger>
            <DialogContent className="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>New Check Call</DialogTitle>
                    <DialogDescription>
                        Record a check call with the carrier
                    </DialogDescription>
                </DialogHeader>
                <form onSubmit={handleSubmit} className="space-y-4">
                    {stops.length > 0 && (
                        <div className="space-y-2">
                            <Label htmlFor="stop_id">Stop</Label>
                            <Select
                                value={data.stop_id.toString()}
                                onValueChange={(value) =>
                                    setData('stop_id', value)
                                }
                            >
                                <SelectTrigger>
                                    <SelectValue placeholder="Select a stop" />
                                </SelectTrigger>
                                <SelectContent>
                                    {stops.map((stop) => (
                                        <SelectItem
                                            key={stop.id}
                                            value={stop.id?.toString() || ''}
                                        >
                                            {stop.facility?.name} -{' '}
                                            {stop.stop_type}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {errors.stop_id && (
                                <p className="text-sm text-red-500">
                                    {errors.stop_id}
                                </p>
                            )}
                        </div>
                    )}

                    <div className="space-y-2">
                        <Label htmlFor="eta">ETA (Optional)</Label>
                        <Input
                            id="eta"
                            type="datetime-local"
                            value={data.eta}
                            onChange={(e) => setData('eta', e.target.value)}
                        />
                        {errors.eta && (
                            <p className="text-sm text-red-500">{errors.eta}</p>
                        )}
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="reported_trailer_temp">
                            Reported Trailer Temp (Optional)
                        </Label>
                        <Input
                            id="reported_trailer_temp"
                            type="number"
                            step="0.1"
                            value={data.reported_trailer_temp}
                            onChange={(e) =>
                                setData('reported_trailer_temp', e.target.value)
                            }
                            placeholder="Enter temperature"
                        />
                        {errors.reported_trailer_temp && (
                            <p className="text-sm text-red-500">
                                {errors.reported_trailer_temp}
                            </p>
                        )}
                    </div>

                    <div className="space-y-2">
                        <div className="flex items-center justify-between">
                            <Label htmlFor="contact_name">
                                Contact Name (Optional)
                            </Label>
                            {carrierContacts.length > 0 && (
                                <Select
                                    onValueChange={(value) => {
                                        const contact = carrierContacts.find(
                                            (c) => c.id.toString() === value,
                                        );
                                        if (contact) selectContact(contact);
                                    }}
                                >
                                    <SelectTrigger className="w-[180px]">
                                        <SelectValue placeholder="Carrier contacts" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {carrierContacts.map((contact) => (
                                            <SelectItem
                                                key={contact.id}
                                                value={contact.id.toString()}
                                            >
                                                {contact.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            )}
                        </div>
                        <Input
                            id="contact_name"
                            value={data.contact_name}
                            onChange={(e) =>
                                setData('contact_name', e.target.value)
                            }
                            placeholder="Who you spoke with"
                        />
                        {errors.contact_name && (
                            <p className="text-sm text-red-500">
                                {errors.contact_name}
                            </p>
                        )}
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                        <div className="space-y-2">
                            <Label htmlFor="contact_method">
                                Contact Method (Optional)
                            </Label>
                            <Select
                                value={data.contact_method}
                                onValueChange={(value) =>
                                    setData('contact_method', value)
                                }
                            >
                                <SelectTrigger>
                                    <SelectValue placeholder="Select method" />
                                </SelectTrigger>
                                <SelectContent>
                                    {contactMethods.map((method) => (
                                        <SelectItem
                                            key={method.value}
                                            value={method.value}
                                        >
                                            {method.label}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {errors.contact_method && (
                                <p className="text-sm text-red-500">
                                    {errors.contact_method}
                                </p>
                            )}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="contact_method_detail">
                                Contact Detail (Optional)
                            </Label>
                            <Input
                                id="contact_method_detail"
                                value={data.contact_method_detail}
                                onChange={(e) =>
                                    setData(
                                        'contact_method_detail',
                                        e.target.value,
                                    )
                                }
                                placeholder="Phone number, email, etc."
                            />
                            {errors.contact_method_detail && (
                                <p className="text-sm text-red-500">
                                    {errors.contact_method_detail}
                                </p>
                            )}
                        </div>
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="arrived_at">
                            Arrived At (Optional)
                        </Label>
                        <Input
                            id="arrived_at"
                            type="datetime-local"
                            value={data.arrived_at}
                            onChange={(e) =>
                                setData('arrived_at', e.target.value)
                            }
                        />
                        {errors.arrived_at && (
                            <p className="text-sm text-red-500">
                                {errors.arrived_at}
                            </p>
                        )}
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="left_at">Left At (Optional)</Label>
                        <Input
                            id="left_at"
                            type="datetime-local"
                            value={data.left_at}
                            onChange={(e) => setData('left_at', e.target.value)}
                        />
                        {errors.left_at && (
                            <p className="text-sm text-red-500">
                                {errors.left_at}
                            </p>
                        )}
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="loaded_unloaded_at">
                            Loaded/Unloaded At (Optional)
                        </Label>
                        <Input
                            id="loaded_unloaded_at"
                            type="datetime-local"
                            value={data.loaded_unloaded_at}
                            onChange={(e) =>
                                setData('loaded_unloaded_at', e.target.value)
                            }
                        />
                        {errors.loaded_unloaded_at && (
                            <p className="text-sm text-red-500">
                                {errors.loaded_unloaded_at}
                            </p>
                        )}
                    </div>

                    <DialogFooter>
                        <Button type="submit" disabled={processing}>
                            Add Check Call
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
