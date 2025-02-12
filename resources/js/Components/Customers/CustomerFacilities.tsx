import FacilityForm from '@/Components/CreateForms/FacilityForm';
import { ResourceSearchSelect } from '@/Components/ResourceSearchSelect';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog';
import { Customer, Facility } from '@/types';
import { useForm } from '@inertiajs/react';
import axios from 'axios';
import { useCallback, useEffect, useState } from 'react';
import { Skeleton } from '../ui/skeleton';

export default function CustomerFacilities({
    customer,
}: {
    customer?: Customer;
}) {
    const [facilityModalOpen, setFacilityModalOpen] = useState(false);
    const { setData: setAttachFacilityData, post: attachFacilityPost } =
        useForm<{ facility_id: string | null }>({
            facility_id: null,
        });

    const [facilities, setFacilities] = useState<Facility[]>([]);
    const [isLoading, setIsLoading] = useState(false);

    const detachFacility = (facility: Facility) => {
        axios
            .delete(
                route('customers.facilities.destroy', {
                    customer: customer?.id,
                    facility: facility.id,
                }),
            )
            .then(() => {
                loadFacilities();
            });
    };

    const loadFacilities = useCallback(() => {
        setIsLoading(true);
        axios
            .get(
                route('customers.facilities.index', {
                    customer: customer.id,
                }),
            )
            .then((response) => {
                setFacilities(response.data);
                setIsLoading(false);
            });
    }, [setFacilities, setIsLoading, customer]);

    useEffect(() => {
        loadFacilities();
    }, [loadFacilities]);

    return (
        <Card>
            <CardHeader>
                <div className="flex items-center justify-between">
                    <CardTitle>Facilities</CardTitle>
                    <Button
                        variant="default"
                        onClick={() => setFacilityModalOpen(true)}
                    >
                        Attach Facility
                    </Button>
                </div>
            </CardHeader>
            <CardContent className="pt-6">
                <div className="flex flex-col gap-2 space-y-4">
                    {isLoading ? (
                        <Skeleton className="h-32 w-full" />
                    ) : (
                        <>
                            {facilities.map((facility) => (
                                <div
                                    key={facility.id}
                                    className="flex items-center justify-between"
                                >
                                    <span>{facility.name}</span>
                                    <Button
                                        variant="destructive"
                                        onClick={() => detachFacility(facility)}
                                    >
                                        Detach
                                    </Button>
                                </div>
                            ))}
                        </>
                    )}
                </div>
            </CardContent>

            <Dialog
                open={facilityModalOpen}
                onOpenChange={setFacilityModalOpen}
            >
                <DialogContent className="max-w-screen-sm">
                    <DialogHeader>
                        <DialogTitle>Attach Facility</DialogTitle>
                    </DialogHeader>
                    <div className="w-full max-w-full">
                        <ResourceSearchSelect
                            searchRoute={route('facilities.search')}
                            onValueChange={(value) => {
                                // Handle the facility selection
                                setAttachFacilityData({
                                    facility_id: value as string,
                                });
                            }}
                            createForm={FacilityForm}
                            allowMultiple={false}
                            className="w-full"
                        />
                    </div>
                    <DialogFooter>
                        <Button
                            variant="outline"
                            onClick={() => setFacilityModalOpen(false)}
                        >
                            Cancel
                        </Button>
                        <Button
                            variant="default"
                            onClick={() => {
                                attachFacilityPost(
                                    route('customers.facilities.store', {
                                        customer: customer?.id,
                                    }),
                                    {
                                        onSuccess: () => {
                                            loadFacilities();

                                            // TODO - show a toast
                                            setFacilityModalOpen(false);
                                        },
                                    },
                                );
                            }}
                        >
                            Attach
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </Card>
    );
}
