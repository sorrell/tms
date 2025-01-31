import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Skeleton } from '@/Components/ui/skeleton';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { FileText, MapPin, MessageSquare, Truck } from 'lucide-react';
import ShipmentHeader from './Partials/ShipmentHeader';
import ShipmentGeneral from './Partials/ShipmentGeneral';
import CarrierDetails from './Partials/CarrierDetails';
import ShipperDetails from './Partials/ShipperDetails';
import { ShipmentStop } from '@/types';
import ShipmentStopsList from './Partials/ShipmentStopsList';

export default function Show({ shipment, stops }: { shipment: any, stops: ShipmentStop[] }) {
    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {
                    title: 'Shipments',
                    url: route('shipments.index'),
                },
                {
                    title: `${shipment.shipment_number ?? `id:${shipment.id}`}`,
                },
            ]}
        >
            <Head title="Load Details" />

            <div className="mx-auto max-w-screen-2xl space-y-4 p-4 md:p-8">
                {/* Header with key load info */}
                <ShipmentHeader shipment={shipment} />

                <div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
                    {/* Main Content Area - 2 columns */}
                    <div className="space-y-4 lg:col-span-2">
                        {/* Map View Card */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <MapPin className="h-5 w-5" />
                                    Location Tracking
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <Skeleton className="h-[300px] w-full" />
                            </CardContent>
                        </Card>

                        {/* Stops Timeline */}
                        <ShipmentStopsList shipmentId={shipment.id} stops={stops} />

                        {/* Tabbed Content */}
                        <Tabs defaultValue="financials">
                            <TabsList className="w-full justify-start">
                                <TabsTrigger value="financials">
                                    Financials
                                </TabsTrigger>
                                <TabsTrigger value="documents">
                                    Documents
                                </TabsTrigger>
                                <TabsTrigger value="notes">Notes</TabsTrigger>
                                <TabsTrigger value="tracking">
                                    Tracking Updates
                                </TabsTrigger>
                            </TabsList>

                            <TabsContent
                                value="financials"
                                className="space-y-4"
                            >
                                {/* Shipper Billing */}
                                <Card>
                                    <CardHeader>
                                        <CardTitle>Shipper Billing</CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        <Skeleton className="h-40 w-full" />
                                    </CardContent>
                                </Card>

                                {/* Carrier Pay */}
                                <Card>
                                    <CardHeader>
                                        <CardTitle>Carrier Pay</CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        <Skeleton className="h-40 w-full" />
                                    </CardContent>
                                </Card>

                                {/* Advances/Charges */}
                                <Card>
                                    <CardHeader>
                                        <CardTitle>
                                            Advances & Additional Charges
                                        </CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        <Skeleton className="h-32 w-full" />
                                    </CardContent>
                                </Card>
                            </TabsContent>

                            <TabsContent value="documents">
                                <Card>
                                    <CardContent className="pt-6">
                                        <div className="grid grid-cols-2 gap-4 md:grid-cols-4">
                                            {[1, 2, 3, 4].map((doc) => (
                                                <Skeleton
                                                    key={doc}
                                                    className="h-40 w-full"
                                                />
                                            ))}
                                        </div>
                                    </CardContent>
                                </Card>
                            </TabsContent>

                            <TabsContent value="notes">
                                <Card>
                                    <CardContent className="pt-6">
                                        <div className="space-y-4">
                                            <div className="flex gap-2">
                                                <Button>Add Note</Button>
                                                <Button variant="outline">
                                                    Filter
                                                </Button>
                                            </div>
                                            {[1, 2, 3].map((note) => (
                                                <Skeleton
                                                    key={note}
                                                    className="h-24 w-full"
                                                />
                                            ))}
                                        </div>
                                    </CardContent>
                                </Card>
                            </TabsContent>

                            <TabsContent value="tracking">
                                <Card>
                                    <CardContent className="pt-6">
                                        <div className="space-y-4">
                                            {[1, 2, 3, 4].map((update) => (
                                                <Skeleton
                                                    key={update}
                                                    className="h-16 w-full"
                                                />
                                            ))}
                                        </div>
                                    </CardContent>
                                </Card>
                            </TabsContent>
                        </Tabs>
                    </div>

                    {/* Sidebar - 1 column */}
                    <div className="space-y-4">
                        {/* General */}
                        <ShipmentGeneral shipment={shipment} />

                        {/* Carrier Details */}
                        <CarrierDetails shipment={shipment} />

                        {/* Shippers */}
                        <ShipperDetails shipment={shipment} />

                        {/* Quick Actions */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Quick Actions</CardTitle>
                            </CardHeader>
                            <CardContent className="flex flex-col gap-2">
                                <Button className="w-full justify-start">
                                    <MessageSquare className="mr-2 h-4 w-4" />
                                    Send Message
                                </Button>
                                <Button
                                    className="w-full justify-start"
                                    variant="outline"
                                >
                                    <FileText className="mr-2 h-4 w-4" />
                                    Upload Document
                                </Button>
                            </CardContent>
                        </Card>

                        {/* Activity Feed */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Recent Activity</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-2">
                                {[1, 2, 3, 4].map((activity) => (
                                    <Skeleton
                                        key={activity}
                                        className="h-12 w-full"
                                    />
                                ))}
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
