import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import {
    Form,
    FormControl,
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
import { Textarea } from '@/Components/ui/textarea';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Carrier, Facility, Shipper } from '@/types';
import { TemperatureUnit } from '@/types/enums';
import { zodResolver } from '@hookform/resolvers/zod';
import { Head, router, usePage } from '@inertiajs/react';
import { ArrowDown, ArrowUp, Trash } from 'lucide-react';
import { useForm } from 'react-hook-form';

import { z } from 'zod';

export default function Create({
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

        // weight: z.number().nullable(),
        // trip_miles: z.number().nullable(),
        // trailer_type_id: z.string().nullable(),
        // trailer_temperature_range: z.boolean().nullable(),
        // trailer_temperature_minimum: z.number().nullable(),
        // trailer_temperature_maximum: z.number().nullable(),
        // trailer_temperature_unit: z.nativeEnum(TemperatureUnit),

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
                    special_instructions: z.string().nullable(),
                    reference_numbers: z.string().nullable(),
                    stop_number: z.number().int(),
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
                    appointment: { datetime: '' },
                    special_instructions: '',
                    reference_numbers: '',
                    stop_number: 1,
                },
                {
                    stop_type: 'delivery',
                    appointment: { datetime: '' },
                    special_instructions: '',
                    reference_numbers: '',
                    stop_number: 2,
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
                    className="mx-auto max-w-screen-2xl space-y-2 px-2 pb-8 md:space-y-8 md:px-8"
                >
                    <Card>
                        <CardHeader>
                            <CardTitle>General</CardTitle>
                        </CardHeader>
                        <CardContent></CardContent>
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
                                                defaultSelectedItems={
                                                    field.value
                                                }
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
                                                defaultSelectedItems={
                                                    field.value
                                                }
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
                                        variant="default"
                                        onClick={() =>
                                            form.setValue('stops', [
                                                ...form.getValues('stops'),
                                                {
                                                    stop_type: 'pickup',
                                                    appointment: {
                                                        datetime: '',
                                                    },
                                                    special_instructions: '',
                                                    reference_numbers: '',
                                                    stop_number:
                                                        form.getValues('stops')
                                                            .length + 1,
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
                        <CardContent className="space-y-8">
                            {form
                                .watch('stops')
                                .map((stop: any, index: number) => (
                                    <div
                                        className="flex items-center justify-between space-x-2"
                                        key={'stops-div-' + index}
                                    >
                                        <div className="flex h-full flex-shrink-0 flex-col justify-between gap-2">
                                            <Button
                                                variant="secondary"
                                                size="icon"
                                                onClick={(e) => {
                                                    e.preventDefault();
                                                    const stops = [
                                                        ...form.getValues(
                                                            'stops',
                                                        ),
                                                    ];
                                                    const temp = stops[index];
                                                    stops[index] =
                                                        stops[index - 1];
                                                    stops[index - 1] = temp;

                                                    stops.forEach((stop, i) => {
                                                        stop.stop_number =
                                                            i + 1;
                                                    });
                                                    form.setValue(
                                                        'stops',
                                                        stops,
                                                        {
                                                            shouldValidate:
                                                                false,
                                                            shouldTouch: false,
                                                        },
                                                    );
                                                }}
                                                disabled={index === 0}
                                            >
                                                <ArrowUp className="h-4 w-4" />
                                            </Button>
                                            <span className="text-center text-sm font-bold">
                                                {stop.stop_number}
                                            </span>
                                            <Button
                                                variant="secondary"
                                                size="icon"
                                                onClick={(e) => {
                                                    e.preventDefault();
                                                    const stops = [
                                                        ...form.getValues(
                                                            'stops',
                                                        ),
                                                    ];
                                                    const temp = stops[index];
                                                    stops[index] =
                                                        stops[index + 1];
                                                    stops[index + 1] = temp;

                                                    stops.forEach((stop, i) => {
                                                        stop.stop_number =
                                                            i + 1;
                                                    });

                                                    form.setValue(
                                                        'stops',
                                                        stops,
                                                        {
                                                            shouldValidate:
                                                                false,
                                                            shouldTouch: false,
                                                        },
                                                    );
                                                }}
                                                disabled={
                                                    index ===
                                                    form.getValues('stops')
                                                        .length -
                                                        1
                                                }
                                            >
                                                <ArrowDown className="h-4 w-4" />
                                            </Button>
                                        </div>
                                        <div
                                            key={'stop' + index}
                                            className="flex flex-grow flex-col space-y-4 border-l-2 border-primary py-2 pl-2 md:border-l-4 md:pl-4"
                                        >
                                            <FormField
                                                key={index}
                                                control={form.control}
                                                name={`stops.${index}.stop_type`}
                                                render={({ field }) => (
                                                    <FormItem className="flex flex-row">
                                                        <FormControl>
                                                            <div className="flex flex-grow">
                                                                <Select
                                                                    onValueChange={
                                                                        field.onChange
                                                                    }
                                                                    value={
                                                                        field.value
                                                                    }
                                                                >
                                                                    <SelectTrigger className="w-fit min-w-[100px]">
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
                                                            </div>
                                                        </FormControl>
                                                        <FormLabel className="">
                                                            <Button
                                                                disabled={form.getValues('stops').length <= 2}
                                                                type="button"
                                                                variant="ghost"
                                                                onClick={() => {
                                                                    let stops = [
                                                                        ...form.getValues('stops'),
                                                                    ];
                                                                    stops.splice(index, 1);

                                                                    stops.forEach(
                                                                        (
                                                                            stop,
                                                                            i,
                                                                        ) => {
                                                                            stop.stop_number =
                                                                                i + 1;
                                                                        },
                                                                    );

                                                                    form.setValue(
                                                                        'stops',
                                                                        stops,
                                                                    );
                                                                }}
                                                            >
                                                                Remove
                                                                <Trash className="h-4 w-4" />
                                                            </Button>
                                                        </FormLabel>
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
                                                    <FormItem className="flex flex-col">
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
                                                                defaultSelectedItems={
                                                                    field.value
                                                                }
                                                                className="w-full"
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
                                                    <FormItem>
                                                        <FormLabel>
                                                            Appointment Date
                                                        </FormLabel>
                                                        <FormControl>
                                                            <Input
                                                                type="datetime-local"
                                                                {...field}
                                                            />
                                                        </FormControl>
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

                                            <FormField
                                                control={form.control}
                                                name={`stops.${index}.special_instructions`}
                                                render={({ field }) => (
                                                    <FormItem>
                                                        <FormLabel>
                                                            Special Instructions
                                                        </FormLabel>
                                                        <FormControl>
                                                            <Textarea
                                                                {...field}
                                                            />
                                                        </FormControl>
                                                        <FormMessage>
                                                            {
                                                                errors[
                                                                    `stops.${index}.special_instructions`
                                                                ]
                                                            }
                                                        </FormMessage>
                                                    </FormItem>
                                                )}
                                            />
                                            <FormField
                                                control={form.control}
                                                name={`stops.${index}.reference_numbers`}
                                                render={({ field }) => (
                                                    <FormItem>
                                                        <FormLabel>
                                                            Reference Numbers
                                                        </FormLabel>
                                                        <FormControl>
                                                            <Textarea
                                                                {...field}
                                                            />
                                                        </FormControl>
                                                        <FormMessage>
                                                            {
                                                                errors[
                                                                    `stops.${index}.reference_numbers`
                                                                ]
                                                            }
                                                        </FormMessage>
                                                    </FormItem>
                                                )}
                                            />
                                        </div>
                                    </div>
                                ))}
                        </CardContent>
                    </Card>

                    <Button type="submit">Create Shipment</Button>
                </form>
            </Form>
        </AuthenticatedLayout>
    );
}
