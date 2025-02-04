import { useState } from "react";
import { Input } from "../ui/input";
import axios from "axios";
import { useToast } from "@/hooks/UseToast";
import { Label } from "../ui/label";
import { cn } from "@/lib/utils";


export default function FacilityForm({
    className,
    onSubmit,
    formRef,
    ...props
}: {
    onSubmit: (data: any) => void;
    formRef?: React.RefObject<HTMLFormElement>;
} & React.ComponentPropsWithoutRef<'form'>) {
    const [facilityName, setFacilityName] = useState('');

    const { toast } = useToast();
    
    const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        
        const data = {
            name: facilityName,
        };

        axios.post(route('facilities.store'), data)
            .then(response => {
                onSubmit(response.data);
            })
            .catch(error => {
                toast({
                    title: 'Error',
                    description: 'Failed to create facility',
                    variant: 'destructive',
                });
                console.error(error);
            });
    };

    return (
        <form ref={formRef} onSubmit={handleSubmit} className={cn('flex flex-col gap-2', className)} {...props}>
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
        </form>
    );
}
