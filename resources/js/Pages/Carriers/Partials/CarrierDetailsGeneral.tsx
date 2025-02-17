import LocationForm from '@/Components/CreateForms/LocationForm';
import Notes from '@/Components/Notes';
import { ResourceSearchSelect } from '@/Components/ResourceSearchSelect';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Skeleton } from '@/Components/ui/skeleton';
import { useToast } from '@/hooks/UseToast';
import { Carrier } from '@/types';
import { Notable } from '@/types/enums';
import axios from 'axios';
import { Check, Pencil, X } from 'lucide-react';
import { useState } from 'react';

interface CarrierDetailsGeneralProps {
    carrier?: Carrier;
}

export default function CarrierDetailsGeneral({
    carrier,
}: CarrierDetailsGeneralProps) {
    const [isEditing, setIsEditing] = useState(false);
    const { toast } = useToast();

    const [formState, setFormState] = useState({
        mc_number: carrier?.mc_number || '',
        dot_number: carrier?.dot_number || '',
        physical_location_id: carrier?.physical_location_id || null,
    });

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
            .catch(() => {
                toast({
                    description: 'Failed to save changes',
                });
            });
    };

    return (
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
                                    onClick={() => setIsEditing(false)}
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
                                    onClick={() => setIsEditing(false)}
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
                                searchRoute={route('locations.search')}
                                onValueChange={(value) =>
                                    setFormState({
                                        ...formState,
                                        physical_location_id: Number(value),
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
                                {carrier.physical_location.address_line_1}
                            </div>
                            {carrier.physical_location.address_line_2 && (
                                <div>
                                    {carrier.physical_location.address_line_2}
                                </div>
                            )}
                            <div>
                                {carrier.physical_location.address_city},{' '}
                                {carrier.physical_location.address_state}{' '}
                                {carrier.physical_location.address_zipcode}
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
    );
}
