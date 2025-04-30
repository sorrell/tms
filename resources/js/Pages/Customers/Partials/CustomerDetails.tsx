import ContactList from '@/Components/Contacts/ContactList/ContactList';
import CustomerFacilities from '@/Components/Customers/CustomerFacilities';
import DocumentsList from '@/Components/Documents/DocumentsList';
import Notes from '@/Components/Notes';
import ShipmentList from '@/Components/Shipments/ShipmentList/ShipmentList';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { ComingSoon } from '@/Components/ui/coming-soon';
import { Input } from '@/Components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import { Skeleton } from '@/Components/ui/skeleton';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs';
import { useMediaQuery } from '@/hooks/useMediaQuery';
import { Customer } from '@/types';
import { Contactable, Documentable, Notable } from '@/types/enums';
import { router } from '@inertiajs/react';
import { Check, Pencil, X } from 'lucide-react';
import { useState } from 'react';

export default function CustomerDetails({ customer }: { customer: Customer }) {
    const [isEditing, setIsEditing] = useState(false);
    const [editedName, setEditedName] = useState(customer?.name || '');

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

    const isMobile = useMediaQuery('(max-width: 768px)');

    const handleSave = () => {
        router.put(
            route('customers.update', customer?.id),
            {
                name: editedName,
            },
            {
                preserveScroll: true,
                onSuccess: () => setIsEditing(false),
            },
        );
    };

    const handleCancel = () => {
        setEditedName(customer?.name || '');
        setIsEditing(false);
    };

    return (
        <div className="flex flex-col gap-6">
            {/* Header Section */}
            <div className="flex items-center justify-between">
                <div className="flex items-center gap-2">
                    {isEditing ? (
                        <>
                            <Input
                                value={editedName}
                                onChange={(e) => setEditedName(e.target.value)}
                                className="h-auto py-0 font-bold md:text-2xl"
                                autoFocus
                            />
                            <button
                                onClick={handleSave}
                                className="p-1 text-success hover:text-success/75"
                            >
                                <Check className="h-5 w-5" />
                            </button>
                            <button
                                onClick={handleCancel}
                                className="p-1 text-destructive hover:text-destructive/75"
                            >
                                <X className="h-5 w-5" />
                            </button>
                        </>
                    ) : (
                        <>
                            <h1 className="text-2xl font-bold">
                                {customer?.name}
                            </h1>
                            <button
                                onClick={() => setIsEditing(true)}
                                className="p-1 text-gray-400 hover:text-gray-600"
                            >
                                <Pencil className="h-4 w-4" />
                            </button>
                        </>
                    )}
                </div>
                <div className="flex gap-2">
                    {/* Space for future buttons */}
                </div>
            </div>

            {/* Main Content */}
            <Tabs defaultValue={tab} value={tab} className="w-full">
                {isMobile ? (
                    <Select value={tab} onValueChange={handleTabChange}>
                        <SelectTrigger className="w-full">
                            <SelectValue placeholder="Select view" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="overview">Overview</SelectItem>
                            <SelectItem value="facilities">
                                Facilities
                            </SelectItem>
                            <SelectItem value="contacts">Contacts</SelectItem>
                            <SelectItem value="documents">Documents</SelectItem>
                            <SelectItem value="shipments">
                                Shipment History
                            </SelectItem>
                        </SelectContent>
                    </Select>
                ) : (
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
                )}

                {/* Overview Tab */}
                <TabsContent value="overview">
                    <div className="grid grid-cols-2 gap-6">
                        {/* Account Information */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Account Information</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <ComingSoon
                                    variant="outline"
                                    className="mx-auto"
                                />
                            </CardContent>
                        </Card>

                        {/* Billing Information */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Billing Information</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <ComingSoon
                                    variant="outline"
                                    className="mx-auto"
                                />
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
                                contactForType={Contactable.Customer}
                            />
                        </CardContent>
                    </Card>
                </TabsContent>

                {/* Documents Tab */}
                <TabsContent value="documents">
                    <Card>
                        <CardContent className="pt-6">
                            <DocumentsList
                                documents={customer.documents ?? []}
                                folders={customer.document_folders ?? []}
                                documentableType={Documentable.Customer}
                                documentableId={customer.id}
                            />
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
