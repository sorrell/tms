import { Input } from '@/Components/ui/input';

import {
    Dialog,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog';

import InputError from '@/Components/InputError';
import { Button } from '@/Components/ui/button';
import { DialogContent } from '@/Components/ui/dialog';
import { Label } from '@/Components/ui/label';
import { router } from '@inertiajs/react';
import axios from 'axios';
import { Search } from 'lucide-react';
import { useState } from 'react';

function CarrierManualCreateForm({
    setIsOpen,
}: {
    setIsOpen: (isOpen: boolean) => void;
}) {
    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setErrors({});
        router.post(
            route('carriers.store'),
            {
                name: carrierName,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    setIsOpen(false);
                    setCarrierName('');
                },
                onError: (errors) => {
                    setErrors(errors);
                },
            },
        );
    };

    const [carrierName, setCarrierName] = useState('');
    const [errors, setErrors] = useState<Record<string, string>>({});

    return (
        <form onSubmit={handleSubmit} className="space-y-4">
            <Input
                placeholder="Carrier name"
                value={carrierName}
                onChange={(e) => setCarrierName(e.target.value)}
            />
            {errors.name && <InputError message={errors.name} />}
            <DialogFooter>
                <Button
                    type="button"
                    variant="outline"
                    onClick={() => setIsOpen(false)}
                >
                    Cancel
                </Button>
                <Button type="submit">Create</Button>
            </DialogFooter>
        </form>
    );
}

function CarrierFmcsaCreateForm({
    setIsOpen,
    setFormState,
}: {
    setIsOpen: (isOpen: boolean) => void;
    setFormState: (formState: 'manual' | 'fmcsa') => void;
}) {
    const [carrierName, setCarrierName] = useState('');
    const [carrierDotNumber, setCarrierDotNumber] = useState('');
    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();
        e.stopPropagation();
        axios
            .get(route('carriers.fmcsa.lookup.name'), {
                params: {
                    name: carrierName,
                },
            })
            .then((response) => {
                console.log(response.data);
            });
    };

    return (
        <form onSubmit={handleSearch} className="space-y-4">
            <div className="space-y-2">
                <Label>Carrier Name</Label>
                <Input
                    placeholder="Carrier name"
                    value={carrierName}
                    onChange={(e) => setCarrierName(e.target.value)}
                />
            </div>
            <div className="space-y-2">
                <Label>DOT Number</Label>
                <Input
                    placeholder="#######"
                    value={carrierDotNumber}
                    onChange={(e) => setCarrierDotNumber(e.target.value)}
                />
            </div>
            <DialogFooter>
                <Button
                    type="button"
                    variant="outline"
                    onClick={() => setIsOpen(false)}
                >
                    Cancel
                </Button>
                <Button type="submit">
                    <Search className="inline" /> Search
                </Button>
            </DialogFooter>
        </form>
    );
}

export default function CarrierCreateDialog({
    isOpen,
    setIsOpen,
}: {
    isOpen: boolean;
    setIsOpen: (isOpen: boolean) => void;
}) {
    const [formState, setFormState] = useState<'manual' | 'fmcsa'>('fmcsa');

    return (
        <Dialog open={isOpen} onOpenChange={setIsOpen}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Create Carrier</DialogTitle>
                </DialogHeader>
                {formState === 'manual' && (
                    <CarrierManualCreateForm setIsOpen={setIsOpen} />
                )}
                {formState === 'fmcsa' && (
                    <CarrierFmcsaCreateForm
                        setIsOpen={setIsOpen}
                        setFormState={setFormState}
                    />
                )}
            </DialogContent>
        </Dialog>
    );
}
