import ContactList from '@/Components/Contacts/ContactList/ContactList';
import LocationForm from '@/Components/CreateForms/LocationForm';
import Notes from '@/Components/Notes';
import { ResourceSearchSelect } from '@/Components/ResourceSearchSelect';
import ShipmentList from '@/Components/Shipments/ShipmentList/ShipmentList';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import { Skeleton } from '@/Components/ui/skeleton';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs';
import { useToast } from '@/hooks/UseToast';
import { useMediaQuery } from '@/hooks/useMediaQuery';
import { Carrier } from '@/types';
import { Contactable, Notable } from '@/types/enums';
import { router } from '@inertiajs/react';
import axios from 'axios';
import { Check, Pencil, X } from 'lucide-react';
import { useState } from 'react';

export default function CarrierDetails({ carrier }: { carrier?: Carrier }) {
    const [isEditing, setIsEditing] = useState(false);
    const { toast } = useToast();

    const [formState, setFormState] = useState({
        mc_number: carrier?.mc_number || '',
        dot_number: carrier?.dot_number || '',
        physical_location_id: carrier?.physical_location_id || null,
    });

    const [errors, setErrors] = useState({});

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

    const handleSave = () => {
        axios
            .put(route('carriers.update', { carrier: carrier?.id }), {
                mc_number: formState.mc_number,
                dot_number: formState.dot_number,
                physical_location_id: formState.physical_location_id,
            })
            .then(() => {
                setIsEditing(false);
                toast({
                    description: 'Changes saved successfully',
                });
            })
            .catch((error) => {
                setErrors(error.response);
                toast({
                    description: 'Failed to save changes',
                });
            });
    };

    const isMobile = useMediaQuery('(max-width: 768px)');

    return (
        <div className="flex flex-col gap-6">
            {/* Header Section */}
            <div className="flex items-center justify-between">
                <h1 className="text-2xl font-bold">{carrier?.name}</h1>
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
                    <div className="grid grid-cols-2 gap-6">
                        {/* General Information */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center justify-between">
                                    General Information
                                    {!isEditing ? (
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            onClick={() => setIsEditing(true)}
                                        >
                                            <Pencil className="h-4 w-4" />
                                        </Button>
                                    ) : (
                                        <div className="flex gap-2">
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                onClick={handleSave}
                                            >
                                                <Check className="h-4 w-4" />
                                            </Button>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                onClick={() =>
                                                    setIsEditing(false)
                                                }
                                            >
                                                <X className="h-4 w-4" />
                                            </Button>
                                        </div>
                                    )}
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div>
                                    <Label
                                        htmlFor="mc_number"
                                        className="text-sm text-gray-500"
                                    >
                                        MC Number
                                    </Label>
                                    {isEditing ? (
                                        <Input
                                            id="mc_number"
                                            value={formState.mc_number}
                                            onChange={(e) =>
                                                setFormState({
                                                    ...formState,
                                                    mc_number: e.target.value,
                                                })
                                            }
                                        />
                                    ) : (
                                        <div className="mt-1">
                                            {carrier?.mc_number || (
                                                <Skeleton className="h-6 w-32" />
                                            )}
                                        </div>
                                    )}
                                </div>
                                <div>
                                    <Label
                                        htmlFor="dot_number"
                                        className="text-sm text-gray-500"
                                    >
                                        DOT Number
                                    </Label>
                                    {isEditing ? (
                                        <Input
                                            id="dot_number"
                                            value={formState.dot_number}
                                            onChange={(e) =>
                                                setFormState({
                                                    ...formState,
                                                    dot_number: e.target.value,
                                                })
                                            }
                                        />
                                    ) : (
                                        <div className="mt-1">
                                            {carrier?.dot_number || (
                                                <Skeleton className="h-6 w-32" />
                                            )}
                                        </div>
                                    )}
                                </div>
                            </CardContent>
                        </Card>

                        {/* Physical Address */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center justify-between">
                                    Physical Address
                                    {!isEditing ? (
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            onClick={() => setIsEditing(true)}
                                        >
                                            <Pencil className="h-4 w-4" />
                                        </Button>
                                    ) : (
                                        <div className="flex gap-2">
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                onClick={handleSave}
                                            >
                                                <Check className="h-4 w-4" />
                                            </Button>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                onClick={() =>
                                                    setIsEditing(false)
                                                }
                                            >
                                                <X className="h-4 w-4" />
                                            </Button>
                                        </div>
                                    )}
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                {isEditing ? (
                                    <div className="flex flex-col gap-4">
                                        <Label>Location</Label>
                                        <ResourceSearchSelect
                                            className="w-full"
                                            searchRoute={route(
                                                'locations.search',
                                            )}
                                            onValueChange={(value) =>
                                                setFormState({
                                                    ...formState,
                                                    physical_location_id:
                                                        Number(value),
                                                })
                                            }
                                            allowMultiple={false}
                                            defaultSelectedItems={formState?.physical_location_id?.toString()}
                                            createForm={LocationForm}
                                        />
                                    </div>
                                ) : carrier?.physical_location ? (
                                    <div className="space-y-2">
                                        <div>
                                            {
                                                carrier.physical_location
                                                    .address_line_1
                                            }
                                        </div>
                                        {carrier.physical_location
                                            .address_line_2 && (
                                            <div>
                                                {
                                                    carrier.physical_location
                                                        .address_line_2
                                                }
                                            </div>
                                        )}
                                        <div>
                                            {
                                                carrier.physical_location
                                                    .address_city
                                            }
                                            ,{' '}
                                            {
                                                carrier.physical_location
                                                    .address_state
                                            }{' '}
                                            {
                                                carrier.physical_location
                                                    .address_zipcode
                                            }
                                        </div>
                                    </div>
                                ) : (
                                    <Skeleton className="h-24 w-full" />
                                )}
                            </CardContent>
                        </Card>

                        {/* Notes Section */}
                        <Card className="col-span-2">
                            <CardHeader>
                                <CardTitle>Notes</CardTitle>
                            </CardHeader>
                            <CardContent>
                                {carrier?.id ? (
                                    <Notes
                                        notableType={Notable.Carrier}
                                        notableId={carrier.id}
                                    />
                                ) : (
                                    <Skeleton className="h-32 w-full" />
                                )}
                            </CardContent>
                        </Card>
                    </div>
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
                                        name: 'carrier.id',
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
