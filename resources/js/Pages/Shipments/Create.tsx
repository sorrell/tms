import { Button } from '@/Components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/Components/ui/card';
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
import { ResourceSearchSelect } from '@/Components/ui/resource-search-select';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import { Skeleton } from '@/Components/ui/skeleton';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Carrier, Facility, Shipper } from '@/types';
import { zodResolver } from '@hookform/resolvers/zod';
import { Head, router, usePage } from '@inertiajs/react';
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
    const { errors } = usePage().props;

    const formSchema = z.object({
        shipper_ids: z.array(z.string()),
        carrier_id: z.string(),
        stops: z
            .array(
                z.object({
                    facility_id: z.string(),
                    stop_type: z.enum(['pickup', 'delivery']),
                    appointment: z.object({
                        datetime: z.string().refine((val) => {
                            // Accept any valid date string that can be parsed
                            const date = new Date(val);
                            return !isNaN(date.getTime());
                        }, 'Please enter a valid date and time'),
                    }),
                }),
            )
            .min(2),
    });

    const form = useForm<z.infer<typeof formSchema>>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            stops: [
                {
                    stop_type: 'pickup',
                    facility_id: facilities[0]?.id.toString(),
                    appointment: { datetime: '' },
                },
                {
                    stop_type: 'delivery',
                    facility_id: facilities[1]?.id.toString(),
                    appointment: { datetime: '' },
                },
            ],
        },
    });

    // 2. Define a submit handler.
    function onSubmit(values: z.infer<typeof formSchema>) {
        router.post(route('shipments.store'), values);
    }

    return (
        <AuthenticatedLayout>
            <Head title="Create Shipment" />

            <Form {...form}>
                <form
                    onSubmit={form.handleSubmit(onSubmit, (errors) => {
                        console.log(errors);
                        console.log(form.getValues());
                    })}
                    className="mx-auto max-w-screen-2xl space-y-8 px-8"
                >
                    <Card>
                        <CardHeader>
                            <CardTitle>General</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Skeleton className="h-[200px] w-full" />
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader>
                            <CardTitle>Shippers</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <FormField
                                control={form.control}
                                name={`shipper_ids`}
                                render={({ field }) => (
                                    <FormItem>
                                        {/* <FormLabel>Shipper</FormLabel> */}
                                        <FormControl>
                                            <ResourceSearchSelect
                                                className="w-full"
                                                searchRoute={route(
                                                    'shippers.search',
                                                )}
                                                onValueChange={field.onChange}
                                                allowMultiple={true}
                                            />
                                        </FormControl>
                                        <FormMessage>
                                            {errors.shipper_ids}
                                        </FormMessage>
                                    </FormItem>
                                )}
                            />
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader>
                            <CardTitle>Carrier</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <FormField
                                control={form.control}
                                name={`carrier_id`}
                                render={({ field }) => (
                                    <FormItem>
                                        {/* <FormLabel>Carrier</FormLabel> */}
                                        <FormControl>
                                            <ResourceSearchSelect
                                                className="w-full"
                                                searchRoute={route(
                                                    'carriers.search',
                                                )}
                                                onValueChange={field.onChange}
                                                allowMultiple={false}
                                            />
                                        </FormControl>
                                        <FormMessage>
                                            {errors.carrier_id}
                                        </FormMessage>
                                    </FormItem>
                                )}
                            />
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader>
                            <CardTitle>
                                <div className="flex items-center justify-between">
                                    <span>Stops</span>
                                    <Button
                                        type="button"
                                        onClick={() =>
                                            form.setValue('stops', [
                                                ...form.getValues('stops'),
                                                {
                                                    stop_type: 'pickup',
                                                    appointment: {
                                                        datetime: '',
                                                    },
                                                    facility_id: '0',
                                                },
                                            ])
                                        }
                                    >
                                        Add Stop
                                    </Button>
                                </div>
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-6">
                            {form
                                .watch('stops')
                                .map((stop: any, index: number) => (
                                    <div key={'stop' + index} className='border-l-4 border-primary pl-4'>
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
                                                                        .getValues(
                                                                            'stops',
                                                                        )
                                                                        .filter(
                                                                            (
                                                                                _,
                                                                                i,
                                                                            ) =>
                                                                                i !==
                                                                                index,
                                                                        ),
                                                                )
                                                            }
                                                        >
                                                            <Trash className="h-4 w-4" />
                                                        </Button>
                                                    </FormLabel>
                                                    <FormControl>
                                                        <Select
                                                            onValueChange={
                                                                field.onChange
                                                            }
                                                            defaultValue={
                                                                field.value
                                                            }
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
                                                    <FormMessage>
                                                        {
                                                            errors[
                                                                `stops.${index}.stop_type`
                                                            ]
                                                        }
                                                    </FormMessage>
                                                </FormItem>
                                            )}
                                        />
                                        <FormField
                                            control={form.control}
                                            name={`stops.${index}.facility_id`}
                                            render={({ field }) => (
                                                <FormItem>
                                                    <FormLabel>
                                                        Facility
                                                    </FormLabel>
                                                    <FormControl>
                                                        <ResourceSearchSelect
                                                            searchRoute={route(
                                                                'facilities.search',
                                                            )}
                                                            allowMultiple={
                                                                false
                                                            }
                                                            onValueChange={
                                                                field.onChange
                                                            }
                                                        />
                                                    </FormControl>
                                                    <FormMessage>
                                                        {
                                                            errors[
                                                                `stops.${index}.facility_id`
                                                            ]
                                                        }
                                                    </FormMessage>
                                                </FormItem>
                                            )}
                                        />
                                        <FormField
                                            control={form.control}
                                            name={`stops.${index}.appointment.datetime`}
                                            render={({ field }) => (
                                                <FormItem className="flex flex-col">
                                                    <FormLabel>
                                                        Appointment Date
                                                    </FormLabel>
                                                    <FormControl>
                                                        <Input
                                                            type="datetime-local"
                                                            {...field}
                                                        />
                                                    </FormControl>
                                                    <FormDescription></FormDescription>
                                                    <FormMessage>
                                                        {
                                                            errors[
                                                                `stops.${index}.appointment.datetime`
                                                            ]
                                                        }
                                                    </FormMessage>
                                                </FormItem>
                                            )}
                                        />
                                    </div>
                                ))}
                        </CardContent>
                    </Card>

                    <Button type="submit">Submit</Button>
                </form>
            </Form>
        </AuthenticatedLayout>
    );
}
