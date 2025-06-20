import { Badge } from '@/Components/ui/badge';
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
import { CreditCard, Settings } from 'lucide-react';
import { useState } from 'react';
import { ShipmentUsage, Subscription } from '@/types/shipment';

export default function BillingForm({
    organization,
    subscription,
    shipmentUsage,
}: {
    organization: Organization;
    subscription?: Subscription;
    shipmentUsage?: ShipmentUsage;
}) {
    const { data, setData, processing, errors, put } = useForm({
        quantity: subscription?.quantity || 1,
    });

    const [isUpdating, setIsUpdating] = useState(false);

    const handleUpdateSeats = (e: React.FormEvent) => {
        e.preventDefault();
        setIsUpdating(true);

        put(route('organizations.billing.update-seats', organization.id), {
            onFinish: () => setIsUpdating(false),
        });
    };

    const handleManageBilling = () => {
        window.location.replace(route('subscriptions.manage', organization.id));
    };

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'active':
                return <Badge variant="default">Active</Badge>;
            case 'trialing':
                return <Badge variant="secondary">Trial</Badge>;
            case 'past_due':
                return <Badge variant="destructive">Past Due</Badge>;
            case 'canceled':
                return <Badge variant="outline">Canceled</Badge>;
            default:
                return <Badge variant="outline">{status}</Badge>;
        }
    };

    const formatDate = (dateString?: string) => {
        if (!dateString) return null;
        return new Date(dateString).toLocaleDateString();
    };

    const monthlyPrice = subscription?.type === 'startup' ? 0 : 50;
    const isStartupPlan = subscription?.type === 'startup';

    return (
        <div className="space-y-6">
            {/* Current Subscription Status */}
            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <CreditCard className="h-5 w-5" />
                        Current Subscription
                    </CardTitle>
                    <CardDescription>
                        Your current billing plan and usage
                    </CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                    {subscription ? (
                        <>
                            <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div>
                                    <Label className="text-sm font-medium text-muted-foreground">
                                        Plan Type
                                    </Label>
                                    <div className="mt-1">
                                        <span className="text-lg font-medium">
                                            {isStartupPlan ? 'Startup' : 'Premium'}
                                        </span>
                                        <div className="text-sm text-muted-foreground">
                                            {isStartupPlan ? 'Single Seat Plan' : 'User Seat Subscription'}
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <Label className="text-sm font-medium text-muted-foreground">
                                        Status
                                    </Label>
                                    <div className="mt-1">
                                        {getStatusBadge(
                                            subscription.stripe_status,
                                        )}
                                    </div>
                                </div>

                                <div>
                                    <Label className="text-sm font-medium text-muted-foreground">
                                        Monthly Total
                                    </Label>
                                    <div className="mt-1">
                                        <span className="text-lg font-medium">
                                            ${monthlyPrice}
                                        </span>
                                        <div className="text-sm text-muted-foreground">
                                            {isStartupPlan ? 'Fixed monthly rate' : `${subscription.quantity} seats × $${monthlyPrice}/month`}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Shipment Usage for Startup Plan */}
                            {shipmentUsage?.is_startup_plan && (
                                <div className="rounded-md border border-blue-200 bg-blue-50 p-4">
                                    <div className="flex items-center justify-between mb-2">
                                        <h4 className="font-medium text-blue-900">Weekly Shipment Usage</h4>
                                        <Badge variant={typeof shipmentUsage.remaining_shipments === 'number' && shipmentUsage.remaining_shipments > 0 ? 'default' : 'destructive'}>
                                            {shipmentUsage.shipments_this_week} / {shipmentUsage.weekly_limit} shipments
                                        </Badge>
                                    </div>
                                    <div className="text-sm text-blue-800">
                                        <p>
                                            You have <strong>{shipmentUsage.remaining_shipments ?? 'unlimited'}</strong> shipments remaining this week.
                                        </p>
                                        {shipmentUsage.week_start && shipmentUsage.week_end && (
                                            <p className="mt-1">
                                                Week: {new Date(shipmentUsage.week_start).toLocaleDateString()} - {new Date(shipmentUsage.week_end).toLocaleDateString()}
                                            </p>
                                        )}
                                        {shipmentUsage.remaining_shipments === 0 && (
                                            <p className="mt-2 font-medium">
                                                ⚠️ Weekly limit reached. Please upgrade to Premium for unlimited shipments.
                                            </p>
                                        )}
                                    </div>
                                </div>
                            )}

                            {subscription.trial_ends_at && (
                                <div className="rounded-md border border-blue-200 bg-blue-50 p-3">
                                    <div className="text-sm">
                                        <strong>Trial Period:</strong> Your
                                        trial ends on{' '}
                                        {formatDate(subscription.trial_ends_at)}
                                    </div>
                                </div>
                            )}
                        </>
                    ) : (
                        <div className="py-8 text-center">
                            <div className="text-muted-foreground">
                                No active subscription found
                            </div>
                        </div>
                    )}
                </CardContent>
            </Card>

            {/* Seat Management - Hidden for Startup Plan */}
            {subscription && !isStartupPlan && (
                <Card>
                    <CardHeader>
                        <CardTitle>Seat Management</CardTitle>
                        <CardDescription>
                            Update the number of seats for your organization
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleUpdateSeats} className="space-y-4">
                            <div>
                                <Label htmlFor="quantity">Number of Seats</Label>
                                <Input
                                    id="quantity"
                                    type="number"
                                    min="1"
                                    value={data.quantity}
                                    onChange={(e) =>
                                        setData('quantity', parseInt(e.target.value))
                                    }
                                    className="mt-1"
                                />
                                {errors.quantity && (
                                    <div className="mt-1 text-sm text-red-600">
                                        {errors.quantity}
                                    </div>
                                )}

                                <div className="mt-1 text-sm text-muted-foreground">
                                    New monthly total: $
                                    {(
                                        data.quantity * monthlyPrice
                                    ).toLocaleString()}
                                </div>
                            </div>

                            <Button
                                type="submit"
                                disabled={
                                    processing ||
                                    isUpdating ||
                                    data.quantity === subscription.quantity
                                }
                                size="sm"
                            >
                                {processing || isUpdating
                                    ? 'Updating...'
                                    : 'Update Seats'}
                            </Button>
                        </form>
                    </CardContent>
                </Card>
            )}

            {/* Startup Plan Upgrade Notice */}
            {isStartupPlan && (
                <Card>
                    <CardHeader>
                        <CardTitle>Upgrade Your Plan</CardTitle>
                        <CardDescription>
                            Ready to grow? Upgrade to Premium for unlimited shipments and team features
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="space-y-4">
                            <div className="text-sm text-muted-foreground">
                                <p><strong>Startup Plan Limitations:</strong></p>
                                <ul className="mt-2 list-disc list-inside space-y-1">
                                    <li>Limited to 10 shipments per week</li>
                                    <li>Single user seat only</li>
                                    <li>No API access</li>
                                    <li>Basic support</li>
                                </ul>
                            </div>
                            <Button
                                onClick={() => window.location.href = route('products-list')}
                                variant="default"
                                className="w-full sm:w-auto"
                            >
                                Upgrade to Premium
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            )}

            {/* Billing Management */}
            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <Settings className="h-5 w-5" />
                        Billing Management
                    </CardTitle>
                    <CardDescription>
                        Manage your payment methods, billing history, and
                        invoices
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <Button
                        onClick={handleManageBilling}
                        variant="outline"
                        className="w-full sm:w-auto"
                    >
                        Open Billing Portal
                    </Button>
                    <div className="mt-2 text-sm text-muted-foreground">
                        You'll be redirected to Stripe's secure billing portal
                        where you can update payment methods, download invoices,
                        and view billing history.
                    </div>
                </CardContent>
            </Card>
        </div>
    );
}
