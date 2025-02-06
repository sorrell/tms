import { useToast } from '@/hooks/UseToast';
import { cn } from '@/lib/utils';
import { CreateFormResult } from '@/types/create-form';
import axios from 'axios';
import { useState } from 'react';
import { ResourceSearchSelect } from '../ResourceSearchSelect';
import { Input } from '../ui/input';
import { Label } from '../ui/label';
import LocationForm from './LocationForm';

export default function FacilityForm({
    className,
    onCreate,
    formRef,
    ...props
}: {
    onCreate: (data: CreateFormResult) => void;
    formRef?: React.RefObject<HTMLFormElement>;
} & React.ComponentPropsWithoutRef<'form'>) {
    const [facilityName, setFacilityName] = useState('');
    const [locationId, setLocationId] = useState<number | null>(null);

    const { toast } = useToast();

    const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        e.stopPropagation();

        const data = {
            name: facilityName,
            location_id: locationId,
        };

        axios
            .post(route('facilities.store'), data)
            .then((response) => {
                onCreate(response.data);
            })
            .catch((error) => {
                toast({
                    title: 'Error',
                    description: 'Failed to create facility',
                    variant: 'destructive',
                });
                console.error(error);
            });
    };

    return (
        <form
            ref={formRef}
            onSubmit={handleSubmit}
            className={cn('flex flex-col gap-2', className)}
            {...props}
        >
            <h2 className="text-lg font-medium">Create Facility</h2>
            <div className="flex flex-col gap-2">
                <Label htmlFor="facility-name">Facility Name</Label>
                <Input
                    id="facility-name"
                    type="text"
                    value={facilityName}
                    onChange={(e) => setFacilityName(e.target.value)}
                />
            </div>
            <div className="flex flex-col gap-2">
                <Label htmlFor="location">Location</Label>
                <ResourceSearchSelect
                    className="w-full"
                    searchRoute={route('locations.search')}
                    onValueChange={(value) => setLocationId(Number(value))}
                    allowMultiple={false}
                    defaultSelectedItems={locationId?.toString()}
                    createForm={LocationForm}
                />
            </div>
        </form>
    );
}
