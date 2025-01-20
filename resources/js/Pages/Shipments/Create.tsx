import { Button } from '@/Components/ui/button';
import {
    Form,
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/Components/ui/form';
import { Input } from '@/Components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Carrier, Facility, Shipper } from '@/types';
import { zodResolver } from '@hookform/resolvers/zod';
import { Head } from '@inertiajs/react';
import { Trash } from 'lucide-react';
import { useForm } from 'react-hook-form';

import { z } from 'zod';

export default function Index({
    facilities,
    shippers,
    carriers,
}: {
    facilities: Facility[];
    shippers: Shipper[];
    carriers: Carrier[];
}) {
    const formSchema = z.object({
        shipper_ids: z.array(z.number()).min(1),
        carrier_id: z.number().min(1),
        stops: z.array(
            z.object({
                facility_id: z.string(),
                stop_type: z.enum(['pickup', 'delivery']),
                appointment: z.object({
                    datetime: z.string().datetime(),
                }),
            }),
        ),
    });

    const form = useForm<z.infer<typeof formSchema>>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            shipper_ids: [],
            carrier_id: carriers[0].id,
            stops: [
                {
                    stop_type: 'pickup',
                    facility_id: '0',
                    appointment: { datetime: '' },
                },
            ],
        },
    });

    // 2. Define a submit handler.
    function onSubmit(values: z.infer<typeof formSchema>) {
        // Do something with the form values.
        // ✅ This will be type-safe and validated.
        console.log(values);
    }

    return (
        <AuthenticatedLayout>
            <Head title="Create Shipment" />

            <Form {...form}>
                <form
                    onSubmit={form.handleSubmit(onSubmit)}
                    className="mx-auto max-w-screen-md space-y-8 px-8"
                >
                    <div>
                        <Button
                            type="button"
                            onClick={() =>
                                form.setValue('stops', [
                                    ...form.getValues('stops'),
                                    {
                                        stop_type: 'pickup',
                                        appointment: { datetime: '' },
                                        facility_id: '0',
                                    },
                                ])
                            }
                        >
                            Add Stop
                        </Button>
                    </div>
                    {form.watch('stops').map((stop: any, index: number) => (
                        <div key={'stop' + index}>
                            <FormField
                                key={index}
                                control={form.control}
                                name={`stops.${index}.stop_type`}
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel className="flex items-center justify-between">
                                            Stop {index + 1} Type
                                            <Button
                                                type="button"
                                                variant="ghost"
                                                onClick={() =>
                                                    form.setValue(
                                                        'stops',
                                                        form
                                                            .getValues('stops')
                                                            .filter(
                                                                (_, i) =>
                                                                    i !== index,
                                                            ),
                                                    )
                                                }
                                            >
                                                <Trash className="h-4 w-4" />
                                            </Button>
                                        </FormLabel>
                                        <FormControl>
                                            <Select
                                                onValueChange={field.onChange}
                                                defaultValue={field.value}
                                            >
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select a stop type" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="pickup">
                                                        Pickup
                                                    </SelectItem>
                                                    <SelectItem value="delivery">
                                                        Delivery
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />
                            <FormField
                                control={form.control}
                                name={`stops.${index}.facility_id`}
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>Facility</FormLabel>
                                        <FormControl>
                                            <Select
                                                onValueChange={field.onChange}
                                                defaultValue={field.value.toString()}
                                            >
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select a stop type" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {facilities.map(
                                                        (facility) => (
                                                            <SelectItem
                                                                key={
                                                                    facility.id
                                                                }
                                                                value={facility.id.toString()}
                                                            >
                                                                {facility.name}
                                                            </SelectItem>
                                                        ),
                                                    )}
                                                </SelectContent>
                                            </Select>
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />
                            <FormField
                                control={form.control}
                                name={`stops.${index}.appointment.datetime`}
                                render={({ field }) => (
                                    <FormItem className="flex flex-col">
                                        <FormLabel>Appointment Date</FormLabel>
                                        <FormControl>
                                            <Input
                                                type="datetime-local"
                                                {...field}
                                            />
                                        </FormControl>
                                        <FormDescription></FormDescription>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />
                        </div>
                    ))}

                    <FormField
                        control={form.control}
                        name={`shipper_ids`}
                        render={({ field }) => (
                            <FormItem>
                                <FormLabel>Shipper</FormLabel>
                                <FormControl>
                                    <Select
                                        onValueChange={field.onChange}
                                        defaultValue={field.value.toString()}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select shippers" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {shippers.map((shipper) => (
                                                <SelectItem
                                                    key={shipper.id}
                                                    value={shipper.id.toString()}
                                                >
                                                    {shipper.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        )}
                    />
                    <FormField
                        control={form.control}
                        name={`carrier_id`}
                        render={({ field }) => (
                            <FormItem>
                                <FormLabel>Carrier</FormLabel>
                                <FormControl>
                                    <Select
                                        onValueChange={field.onChange}
                                        defaultValue={field.value.toString()}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select a carrier" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {carriers.map((carrier) => (
                                                <SelectItem
                                                    key={carrier.id}
                                                    value={carrier.id.toString()}
                                                >
                                                    {carrier.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        )}
                    />
                    <Button type="submit">Submit</Button>
                </form>
            </Form>
        </AuthenticatedLayout>
    );
}
