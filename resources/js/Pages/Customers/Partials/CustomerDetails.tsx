import ContactList from '@/Components/Contacts/ContactList/ContactList';
import CustomerFacilities from '@/Components/Customers/CustomerFacilities';
import Notes from '@/Components/Notes';
import ShipmentList from '@/Components/Shipments/ShipmentList/ShipmentList';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Skeleton } from '@/Components/ui/skeleton';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs';
import { Customer } from '@/types';
import { Notable } from '@/types/enums';
import { router } from '@inertiajs/react';
import { useState } from 'react';

export default function CustomerDetails({ customer }: { customer?: Customer }) {
    // Get initial tab from URL or default to 'overview'
    const [tab, setTab] = useState(() => {
        const params = new URLSearchParams(window.location.search);
        return params.get('tab') || 'overview';
    });

    // Update URL when tab changes
    const handleTabChange = (value: string) => {
        setTab(value);
        router.push({
            url: route('customers.show', {
                customer: customer?.id,
                tab: value,
            }),
        });
    };

    return (
        <div className="flex flex-col gap-6">
            {/* Header Section */}
            <div className="flex items-center justify-between">
                <h1 className="text-2xl font-bold">{customer?.name}</h1>
                <div className="flex gap-2">
                    {/* Placeholder for action buttons */}
                    <Skeleton className="h-10 w-24" />
                    <Skeleton className="h-10 w-24" />
                </div>
            </div>

            {/* Main Content */}
            <Tabs defaultValue={tab} value={tab} className="w-full">
                <TabsList>
                    <TabsTrigger
                        value="overview"
                        onClick={() => handleTabChange('overview')}
                    >
                        Overview
                    </TabsTrigger>
                    <TabsTrigger
                        value="facilities"
                        onClick={() => handleTabChange('facilities')}
                    >
                        Facilities
                    </TabsTrigger>
                    <TabsTrigger
                        value="contacts"
                        onClick={() => handleTabChange('contacts')}
                    >
                        Contacts
                    </TabsTrigger>
                    <TabsTrigger
                        value="documents"
                        onClick={() => handleTabChange('documents')}
                    >
                        Documents
                    </TabsTrigger>
                    <TabsTrigger
                        value="shipments"
                        onClick={() => handleTabChange('shipments')}
                    >
                        Shipment History
                    </TabsTrigger>
                </TabsList>

                {/* Overview Tab */}
                <TabsContent value="overview">
                    <div className="grid grid-cols-2 gap-6">
                        {/* Account Information */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Account Information</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="grid grid-cols-2 gap-4">
                                    <div>
                                        <div className="text-sm text-gray-500">
                                            Open AR
                                        </div>
                                        <Skeleton className="mt-1 h-6 w-24" />
                                    </div>
                                    <div>
                                        <div className="text-sm text-gray-500">
                                            Closed AR
                                        </div>
                                        <Skeleton className="mt-1 h-6 w-24" />
                                    </div>
                                    <div>
                                        <div className="text-sm text-gray-500">
                                            Credit Line
                                        </div>
                                        <Skeleton className="mt-1 h-6 w-24" />
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Billing Information */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Billing Information</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-4">
                                    <div>
                                        <div className="text-sm text-gray-500">
                                            Invoice Preferences
                                        </div>
                                        <Skeleton className="mt-1 h-6 w-full" />
                                    </div>
                                    <div>
                                        <div className="text-sm text-gray-500">
                                            Billing Address
                                        </div>
                                        <Skeleton className="mt-1 h-20 w-full" />
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Notes Section */}
                        <Card className="col-span-2">
                            <CardHeader>
                                <CardTitle>Notes</CardTitle>
                            </CardHeader>
                            <CardContent>
                                {customer?.id ? (
                                    <Notes
                                        notableType={Notable.Customer}
                                        notableId={customer.id}
                                    />
                                ) : (
                                    <Skeleton className="h-32 w-full" />
                                )}
                            </CardContent>
                        </Card>
                    </div>
                </TabsContent>

                {/* Facilities Tab */}
                <TabsContent value="facilities">
                    <CustomerFacilities customer={customer} />
                </TabsContent>

                {/* Contacts Tab */}
                <TabsContent value="contacts">
                    <Card>
                        <CardContent className="pt-6">
                            <ContactList
                                contactForId={customer?.id || 0}
                                contactForType={`App\\Models\\Customers\\Customer`}
                            />
                        </CardContent>
                    </Card>
                </TabsContent>

                {/* Documents Tab */}
                <TabsContent value="documents">
                    <Card>
                        <CardContent className="pt-6">
                            <div className="space-y-4">
                                <Skeleton className="h-16 w-full" />
                                <Skeleton className="h-16 w-full" />
                                <Skeleton className="h-16 w-full" />
                            </div>
                        </CardContent>
                    </Card>
                </TabsContent>

                {/* Shipment History Tab */}
                <TabsContent value="shipments">
                    <Card>
                        <CardContent className="pt-6">
                            <ShipmentList
                                requiredFilters={[
                                    {
                                        name: 'customers.id',
                                        value: customer?.id?.toString() || '',
                                    },
                                ]}
                            />
                        </CardContent>
                    </Card>
                </TabsContent>
            </Tabs>
        </div>
    );
}
