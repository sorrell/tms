import ContactList from '@/Components/Contacts/ContactList/ContactList';
import ShipmentList from '@/Components/Shipments/ShipmentList/ShipmentList';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
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
import { Carrier } from '@/types';
import { Contactable } from '@/types/enums';
import { router } from '@inertiajs/react';
import { Check, Pencil, X } from 'lucide-react';
import { useState } from 'react';
import CarrierDetailsGeneral from './CarrierDetailsGeneral';

export default function CarrierDetails({ carrier }: { carrier?: Carrier }) {
    const [isEditing, setIsEditing] = useState(false);
    const [editedName, setEditedName] = useState(carrier?.name || '');

    // Get initial tab from URL or default to 'overview'
    const [tab, setTab] = useState(() => {
        const params = new URLSearchParams(window.location.search);
        return params.get('tab') || 'overview';
    });

    // Update URL when tab changes
    const handleTabChange = (value: string) => {
        setTab(value);
        router.push({
            url: route('carriers.show', {
                carrier: carrier?.id,
                tab: value,
            }),
        });
    };

    const isMobile = useMediaQuery('(max-width: 768px)');

    const handleSave = () => {
        router.put(
            route('carriers.update', carrier?.id),
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
        setEditedName(carrier?.name || '');
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
                                className="p-1 text-confirm-600 hover:text-confirm-700"
                            >
                                <Check className="h-5 w-5" />
                            </button>
                            <button
                                onClick={handleCancel}
                                className="p-1 text-cancel-600 hover:text-cancel-700"
                            >
                                <X className="h-5 w-5" />
                            </button>
                        </>
                    ) : (
                        <>
                            <h1 className="text-2xl font-bold">
                                {carrier?.name}
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
                            <SelectItem value="accounting">
                                Accounting
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
                            value="accounting"
                            onClick={() => handleTabChange('accounting')}
                        >
                            Accounting
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
                    <CarrierDetailsGeneral carrier={carrier} />
                </TabsContent>

                {/* Accounting Tab */}
                <TabsContent value="accounting">
                    <Card>
                        <CardHeader>
                            <CardTitle>Billing Information</CardTitle>
                        </CardHeader>
                        <CardContent>
                            {carrier?.billing_location ? (
                                <div className="space-y-4">
                                    <div>
                                        <div className="text-sm text-gray-500">
                                            Billing Email
                                        </div>
                                        <div>{carrier.billing_email}</div>
                                    </div>
                                    <div>
                                        <div className="text-sm text-gray-500">
                                            Billing Phone
                                        </div>
                                        <div>{carrier.billing_phone}</div>
                                    </div>
                                    <div>
                                        <div className="text-sm text-gray-500">
                                            Billing Address
                                        </div>
                                        <div className="space-y-2">
                                            <div>
                                                {
                                                    carrier.billing_location
                                                        .address_line_1
                                                }
                                            </div>
                                            {carrier.billing_location
                                                .address_line_2 && (
                                                <div>
                                                    {
                                                        carrier.billing_location
                                                            .address_line_2
                                                    }
                                                </div>
                                            )}
                                            <div>
                                                {
                                                    carrier.billing_location
                                                        .address_city
                                                }
                                                ,{' '}
                                                {
                                                    carrier.billing_location
                                                        .address_state
                                                }{' '}
                                                {
                                                    carrier.billing_location
                                                        .address_zipcode
                                                }
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ) : (
                                <Skeleton className="h-48 w-full" />
                            )}
                        </CardContent>
                    </Card>
                </TabsContent>

                {/* Contacts Tab */}
                <TabsContent value="contacts">
                    <Card>
                        <CardContent className="pt-6">
                            <ContactList
                                contactForId={carrier?.id || 0}
                                contactForType={Contactable.Carrier}
                            />
                        </CardContent>
                    </Card>
                </TabsContent>

                {/* Documents Tab */}
                <TabsContent value="documents">
                    <Card>
                        <CardContent className="pt-6">
                            <div className="space-y-4">
                                <Skeleton className="h-64 w-full" />
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
                                        name: 'carrier_id',
                                        value: carrier?.id?.toString() || '',
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
