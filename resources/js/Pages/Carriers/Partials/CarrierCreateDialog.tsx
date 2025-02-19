import { Input } from '@/Components/ui/input';

import {
    Dialog,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog';

import InputError from '@/Components/InputError';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { DialogContent } from '@/Components/ui/dialog';
import { Label } from '@/Components/ui/label';
import { Skeleton } from '@/Components/ui/skeleton';
import { CarrierSaferReport } from '@/types';
import { router } from '@inertiajs/react';
import axios from 'axios';
import { ArrowLeft, ArrowRight, Search } from 'lucide-react';
import { useState } from 'react';

function CarrierManualCreateForm({
    setIsOpen,
    setFormState,
}: {
    setIsOpen: (isOpen: boolean) => void;
    setFormState: (formState: 'manual' | 'fmcsa') => void;
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
                    onClick={() => setFormState('fmcsa')}
                >
                    <ArrowLeft className="inline" /> Back to search
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

    const [possibleCarriers, setPossibleCarriers] = useState<
        CarrierSaferReport[]
    >([]);

    const [showCarrierSelectList, setShowCarrierSelectList] = useState(false);

    const [isLoading, setIsLoading] = useState(false);

    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();
        e.stopPropagation();
        setIsLoading(true);
        setShowCarrierSelectList(true);
        axios
            .get(route('carriers.fmcsa.lookup.name'), {
                params: {
                    name: carrierName,
                },
            })
            .then((response) => {
                setPossibleCarriers(response.data);
            })
            .finally(() => {
                setIsLoading(false);
            });
    };

    return (
        <>
            {!showCarrierSelectList && (
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
                        <Label className="text-muted-foreground">
                            DOT Number
                        </Label>
                        <Input
                            disabled={true}
                            placeholder="#######"
                            value={carrierDotNumber}
                            onChange={(e) =>
                                setCarrierDotNumber(e.target.value)
                            }
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
            )}

            {showCarrierSelectList && (
                <div className="space-y-2">
                    <div className="max-h-[500px] space-y-2 overflow-y-auto">
                        {isLoading &&
                            [1, 2, 3].map((i) => (
                                <Skeleton key={i} className="h-40 w-full" />
                            ))}
                        {!isLoading && possibleCarriers.length < 1 && (
                            <p className="text-center text-muted-foreground">
                                No carriers found
                            </p>
                        )}
                        {!isLoading &&
                            possibleCarriers.map((carrier) => (
                                <Card key={carrier.id} className="py-0">
                                    <CardHeader className="md:p-4">
                                        <CardTitle>
                                            {carrier.report.name}
                                        </CardTitle>
                                    </CardHeader>
                                    <CardContent className="flex justify-between px-4">
                                        <div className="flex-grow space-y-2">
                                            <div className="text-sm">
                                                <p>
                                                    {
                                                        carrier.report.address
                                                            .street
                                                    }
                                                </p>
                                                <p>
                                                    {
                                                        carrier.report.address
                                                            .city
                                                    }
                                                    ,
                                                    {
                                                        carrier.report.address
                                                            .state
                                                    }{' '}
                                                    {carrier.report.address.zip}
                                                </p>
                                            </div>
                                            <p className="text-sm">
                                                <p className="mr-2 inline text-muted-foreground">
                                                    DOT:
                                                </p>
                                                {carrier.dot_number}
                                            </p>
                                        </div>
                                        <div className="items-end">
                                            <Button
                                                type="button"
                                                onClick={() =>
                                                    router.post(
                                                        route(
                                                            'carriers.fmcsa.store',
                                                            {
                                                                carrierSaferReport:
                                                                    carrier.id,
                                                            },
                                                        ),
                                                    )
                                                }
                                            >
                                                Create{' '}
                                                <ArrowRight className="inline" />{' '}
                                            </Button>
                                        </div>
                                    </CardContent>
                                </Card>
                            ))}
                    </div>
                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            disabled={isLoading}
                            onClick={() => setFormState('manual')}
                        >
                            Create Manually
                        </Button>
                        <Button
                            type="button"
                            variant="outline"
                            disabled={isLoading}
                            onClick={() => setShowCarrierSelectList(false)}
                        >
                            Search again <Search className="inline" />
                        </Button>
                    </DialogFooter>
                </div>
            )}
        </>
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
                    <CarrierManualCreateForm
                        setIsOpen={setIsOpen}
                        setFormState={setFormState}
                    />
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
