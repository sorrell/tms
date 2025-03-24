import DocumentsList from '@/Components/Documents/DocumentsList';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Skeleton } from '@/Components/ui/skeleton';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Shipment, ShipmentStop, TrailerSize, TrailerType } from '@/types';
import { Documentable } from '@/types/enums';
import { Head } from '@inertiajs/react';
import { FileText, MapPin, MessageSquare } from 'lucide-react';
import CarrierDetails from './Partials/CarrierDetails';
import CustomerDetails from './Partials/CustomerDetails';
import ShipmentGeneral from './Partials/ShipmentGeneral';
import ShipmentHeader from './Partials/ShipmentHeader';
import ShipmentNotes from './Partials/ShipmentNotes';
import ShipmentStopsList from './Partials/ShipmentStopsList';

export default function Show({
    shipment,
    stops,
    trailerTypes,
    trailerSizes,
}: {
    shipment: Shipment;
    stops: ShipmentStop[];
    trailerTypes: TrailerType[];
    trailerSizes: TrailerSize[];
}) {
    return (
        <AuthenticatedLayout
            breadcrumbs={[
                {
                    title: 'Shipments',
                    href: route('shipments.index'),
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
                        <ShipmentStopsList
                            shipmentId={shipment.id}
                            stops={stops}
                        />

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
                                    Tracking
                                </TabsTrigger>
                            </TabsList>

                            <TabsContent
                                value="financials"
                                className="space-y-4"
                            >
                                {/* Customer Billing */}
                                <Card>
                                    <CardHeader>
                                        <CardTitle>Customer Billing</CardTitle>
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
                                        <DocumentsList
                                            documents={shipment.documents ?? []}
                                            folders={
                                                shipment.document_folders ?? []
                                            }
                                            documentableType={
                                                Documentable.Shipment
                                            }
                                            documentableId={shipment.id}
                                        />
                                    </CardContent>
                                </Card>
                            </TabsContent>

                            <TabsContent value="notes">
                                <ShipmentNotes shipmentId={shipment.id} />
                            </TabsContent>

                            <TabsContent value="tracking">
                                <Card>
                                    <CardContent className="pt-6">
                                        <div className="flex items-center justify-center p-4 border border-dashed rounded-md">
                                            <p className="text-muted-foreground text-sm">Tracking feed coming soon</p>
                                        </div>
                                    </CardContent>
                                </Card>
                            </TabsContent>
                        </Tabs>
                    </div>

                    {/* Sidebar - 1 column */}
                    <div className="space-y-4">
                        {/* General */}
                        <ShipmentGeneral
                            shipment={shipment}
                            trailerTypes={trailerTypes}
                            trailerSizes={trailerSizes}
                        />

                        {/* Carrier Details */}
                        <CarrierDetails shipment={shipment} />

                        {/* Customers */}
                        <CustomerDetails shipment={shipment} />


                        {/* Activity Feed */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Recent Activity</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-2">
                                <div className="flex items-center justify-center p-4 border border-dashed rounded-md">
                                    <p className="text-muted-foreground text-sm">Activity feed coming soon</p>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
