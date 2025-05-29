import { Button } from '@/Components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Organization } from '@/types/organization';
import { useForm } from '@inertiajs/react';

export default function SettingsForm({
    organization,
}: {
    organization: Organization;
}) {
    const { data, setData, put, processing, errors } = useForm({
        name: organization.name || '',
        company_name: organization.company_name || '',
        company_address: organization.company_address || '',
        company_city: organization.company_city || '',
        company_state: organization.company_state || '',
        company_zip: organization.company_zip || '',
        company_phone: organization.company_phone || '',
        company_email: organization.company_email || '',
        accounting_contact_email: organization.accounting_contact_email || '',
        accounting_contact_phone: organization.accounting_contact_phone || '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('organizations.settings.update', organization.id));
    };

    return (
        <form onSubmit={submit} className="space-y-6">
            {/* General Settings */}
            <Card>
                <CardHeader>
                    <CardTitle>General Settings</CardTitle>
                    <CardDescription>
                        Basic organization information
                    </CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                    <div>
                        <Label htmlFor="name">Organization Name</Label>
                        <Input
                            id="name"
                            type="text"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            required
                        />
                        {errors.name && (
                            <p className="text-sm text-red-600">
                                {errors.name}
                            </p>
                        )}
                    </div>
                </CardContent>
            </Card>

            {/* Company Information */}
            <Card>
                <CardHeader>
                    <CardTitle>Company Information</CardTitle>
                    <CardDescription>
                        Company details used on invoices and documents
                    </CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                    <div>
                        <Label htmlFor="company_name">Company Name</Label>
                        <Input
                            id="company_name"
                            type="text"
                            value={data.company_name}
                            onChange={(e) =>
                                setData('company_name', e.target.value)
                            }
                        />
                        {errors.company_name && (
                            <p className="text-sm text-red-600">
                                {errors.company_name}
                            </p>
                        )}
                    </div>

                    <div>
                        <Label htmlFor="company_address">Address</Label>
                        <Input
                            id="company_address"
                            type="text"
                            value={data.company_address}
                            onChange={(e) =>
                                setData('company_address', e.target.value)
                            }
                        />
                        {errors.company_address && (
                            <p className="text-sm text-red-600">
                                {errors.company_address}
                            </p>
                        )}
                    </div>

                    <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <Label htmlFor="company_city">City</Label>
                            <Input
                                id="company_city"
                                type="text"
                                value={data.company_city}
                                onChange={(e) =>
                                    setData('company_city', e.target.value)
                                }
                            />
                            {errors.company_city && (
                                <p className="text-sm text-red-600">
                                    {errors.company_city}
                                </p>
                            )}
                        </div>

                        <div>
                            <Label htmlFor="company_state">State</Label>
                            <Input
                                id="company_state"
                                type="text"
                                value={data.company_state}
                                onChange={(e) =>
                                    setData('company_state', e.target.value)
                                }
                                maxLength={2}
                                placeholder="CA"
                            />
                            {errors.company_state && (
                                <p className="text-sm text-red-600">
                                    {errors.company_state}
                                </p>
                            )}
                        </div>

                        <div>
                            <Label htmlFor="company_zip">ZIP Code</Label>
                            <Input
                                id="company_zip"
                                type="text"
                                value={data.company_zip}
                                onChange={(e) =>
                                    setData('company_zip', e.target.value)
                                }
                            />
                            {errors.company_zip && (
                                <p className="text-sm text-red-600">
                                    {errors.company_zip}
                                </p>
                            )}
                        </div>
                    </div>

                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <Label htmlFor="company_phone">Phone</Label>
                            <Input
                                id="company_phone"
                                type="tel"
                                value={data.company_phone}
                                onChange={(e) =>
                                    setData('company_phone', e.target.value)
                                }
                            />
                            {errors.company_phone && (
                                <p className="text-sm text-red-600">
                                    {errors.company_phone}
                                </p>
                            )}
                        </div>

                        <div>
                            <Label htmlFor="company_email">Email</Label>
                            <Input
                                id="company_email"
                                type="email"
                                value={data.company_email}
                                onChange={(e) =>
                                    setData('company_email', e.target.value)
                                }
                            />
                            {errors.company_email && (
                                <p className="text-sm text-red-600">
                                    {errors.company_email}
                                </p>
                            )}
                        </div>
                    </div>
                </CardContent>
            </Card>

            {/* Accounting Contact */}
            <Card>
                <CardHeader>
                    <CardTitle>Accounting Contact</CardTitle>
                    <CardDescription>
                        Dedicated accounting contact information
                    </CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <Label htmlFor="accounting_contact_email">
                                Accounting Email
                            </Label>
                            <Input
                                id="accounting_contact_email"
                                type="email"
                                value={data.accounting_contact_email}
                                onChange={(e) =>
                                    setData(
                                        'accounting_contact_email',
                                        e.target.value,
                                    )
                                }
                            />
                            {errors.accounting_contact_email && (
                                <p className="text-sm text-red-600">
                                    {errors.accounting_contact_email}
                                </p>
                            )}
                        </div>

                        <div>
                            <Label htmlFor="accounting_contact_phone">
                                Accounting Phone
                            </Label>
                            <Input
                                id="accounting_contact_phone"
                                type="tel"
                                value={data.accounting_contact_phone}
                                onChange={(e) =>
                                    setData(
                                        'accounting_contact_phone',
                                        e.target.value,
                                    )
                                }
                            />
                            {errors.accounting_contact_phone && (
                                <p className="text-sm text-red-600">
                                    {errors.accounting_contact_phone}
                                </p>
                            )}
                        </div>
                    </div>
                </CardContent>
            </Card>

            <div className="flex justify-end">
                <Button type="submit" disabled={processing}>
                    {processing ? 'Saving...' : 'Save Settings'}
                </Button>
            </div>
        </form>
    );
}
