import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Switch } from '@/Components/ui/switch';
import { Badge } from '@/Components/ui/badge';
import { Alert, AlertDescription } from '@/Components/ui/alert';
import InputError from '@/Components/InputError';
import { useToast } from '@/hooks/UseToast';
import { Organization } from '@/types/organization';
import { Head, useForm } from '@inertiajs/react';
import { Eye, EyeOff, TestTube, CheckCircle, AlertTriangle, Loader2 } from 'lucide-react';
import { useState } from 'react';

interface HighwayConfiguration {
    api_key: string;
    environment: 'staging' | 'production';
    auto_sync_enabled: boolean;
    sync_frequency: string;
    is_configured: boolean;
    created_at?: string;
    updated_at?: string;
}

interface IntegrationStatus {
    configured: boolean;
    status: 'healthy' | 'errors' | 'stale' | 'not_configured';
    environment?: string;
    carriers_monitored: number;
    last_sync?: string;
    sync_errors: number;
    total_sync_attempts: number;
}

export default function HighwayConfiguration({
    organization,
    configuration,
    integrationStatus,
}: {
    organization: Organization;
    configuration: HighwayConfiguration;
    integrationStatus: IntegrationStatus;
}) {
    const { toast } = useToast();
    const [showApiKey, setShowApiKey] = useState(false);
    const [testingConnection, setTestingConnection] = useState(false);
    const [connectionTestResult, setConnectionTestResult] = useState<{
        success: boolean;
        message: string;
        response_time?: number;
    } | null>(null);

    const { data, setData, post, processing, errors, reset } = useForm({
        api_key: configuration.api_key || '',
        environment: configuration.environment || 'staging',
        auto_sync_enabled: configuration.auto_sync_enabled ?? true,
        sync_frequency: configuration.sync_frequency || 'daily',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        
        post(route('settings.integrations.highway.store', { organization: organization.id }), {
            preserveScroll: true,
            onSuccess: () => {
                toast({
                    description: 'Highway configuration saved successfully!',
                });
            },
            onError: () => {
                toast({
                    description: 'Failed to save Highway configuration',
                    variant: 'destructive',
                });
            },
        });
    };

    const testConnection = async () => {
        setTestingConnection(true);
        setConnectionTestResult(null);

        try {
            const response = await fetch(
                route('settings.integrations.highway.test', { organization: organization.id }),
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    },
                    body: JSON.stringify({
                        api_key: data.api_key,
                        environment: data.environment,
                    }),
                }
            );

            const result = await response.json();
            setConnectionTestResult(result);

            if (result.success) {
                toast({
                    description: 'Connection test successful!',
                });
            } else {
                toast({
                    description: 'Connection test failed',
                    variant: 'destructive',
                });
            }
        } catch (error) {
            setConnectionTestResult({
                success: false,
                message: 'Network error occurred',
            });
            toast({
                description: 'Failed to test connection',
                variant: 'destructive',
            });
        } finally {
            setTestingConnection(false);
        }
    };

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'healthy':
                return <Badge className="bg-green-100 text-green-800 border-green-200">Healthy</Badge>;
            case 'errors':
                return <Badge variant="destructive">Errors</Badge>;
            case 'stale':
                return <Badge className="bg-yellow-100 text-yellow-800 border-yellow-200">Stale</Badge>;
            default:
                return <Badge variant="secondary">Not Configured</Badge>;
        }
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { title: 'Settings' },
                { title: 'Integrations', href: route('settings.integrations.index', { organization: organization.id }) },
                { title: 'Highway' },
            ]}
        >
            <Head title="Highway Integration" />
            
            <div className="mx-4 mb-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold mb-2">Highway Integration</h1>
                        <p className="text-muted-foreground">
                            Configure Highway carrier monitoring and sync settings.
                        </p>
                    </div>
                    {getStatusBadge(integrationStatus.status)}
                </div>
            </div>

            <div className="mx-4 space-y-6">
                {/* Status Overview */}
                {integrationStatus.configured && (
                    <Card>
                        <CardHeader>
                            <CardTitle>Integration Status</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div className="text-center">
                                    <div className="text-2xl font-bold text-blue-600">
                                        {integrationStatus.carriers_monitored}
                                    </div>
                                    <div className="text-sm text-muted-foreground">Carriers Monitored</div>
                                </div>
                                <div className="text-center">
                                    <div className="text-2xl font-bold text-green-600">
                                        {integrationStatus.total_sync_attempts - integrationStatus.sync_errors}
                                    </div>
                                    <div className="text-sm text-muted-foreground">Successful Syncs</div>
                                </div>
                                <div className="text-center">
                                    <div className="text-2xl font-bold text-red-600">
                                        {integrationStatus.sync_errors}
                                    </div>
                                    <div className="text-sm text-muted-foreground">Sync Errors</div>
                                </div>
                                <div className="text-center">
                                    <div className="text-sm font-medium">
                                        {integrationStatus.last_sync 
                                            ? new Date(integrationStatus.last_sync).toLocaleDateString()
                                            : 'Never'
                                        }
                                    </div>
                                    <div className="text-sm text-muted-foreground">Last Sync</div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                )}

                {/* Configuration Form */}
                <Card>
                    <CardHeader>
                        <CardTitle>API Configuration</CardTitle>
                        <CardDescription>
                            Configure your Highway API credentials and environment settings.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-6">
                            {/* Environment Selection */}
                            <div className="space-y-2">
                                <Label htmlFor="environment">Environment</Label>
                                <Select 
                                    value={data.environment} 
                                    onValueChange={(value) => setData('environment', value as 'staging' | 'production')}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select environment" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="staging">Staging</SelectItem>
                                        <SelectItem value="production">Production</SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError message={errors.environment} />
                            </div>

                            {/* API Key */}
                            <div className="space-y-2">
                                <Label htmlFor="api_key">API Key</Label>
                                <div className="flex gap-2">
                                    <div className="relative flex-1">
                                        <Input
                                            id="api_key"
                                            type={showApiKey ? 'text' : 'password'}
                                            value={data.api_key}
                                            onChange={(e) => setData('api_key', e.target.value)}
                                            placeholder="Enter Highway API key"
                                            className="pr-10"
                                        />
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="sm"
                                            className="absolute right-0 top-0 h-full px-3"
                                            onClick={() => setShowApiKey(!showApiKey)}
                                        >
                                            {showApiKey ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                                        </Button>
                                    </div>
                                    <Button
                                        type="button"
                                        variant="outline"
                                        onClick={testConnection}
                                        disabled={!data.api_key || testingConnection}
                                    >
                                        {testingConnection ? (
                                            <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                                        ) : (
                                            <TestTube className="mr-2 h-4 w-4" />
                                        )}
                                        Test
                                    </Button>
                                </div>
                                <InputError message={errors.api_key} />
                                
                                {/* Connection Test Result */}
                                {connectionTestResult && (
                                    <Alert className={connectionTestResult.success ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'}>
                                        <div className="flex items-center gap-2">
                                            {connectionTestResult.success ? (
                                                <CheckCircle className="h-4 w-4 text-green-600" />
                                            ) : (
                                                <AlertTriangle className="h-4 w-4 text-red-600" />
                                            )}
                                            <AlertDescription>
                                                {connectionTestResult.message}
                                                {connectionTestResult.response_time && (
                                                    <span className="ml-2 text-sm text-muted-foreground">
                                                        ({Math.round(connectionTestResult.response_time * 1000)}ms)
                                                    </span>
                                                )}
                                            </AlertDescription>
                                        </div>
                                    </Alert>
                                )}
                            </div>

                            {/* Sync Settings */}
                            <div className="space-y-4">
                                <h3 className="text-lg font-medium">Sync Settings</h3>
                                
                                <div className="flex items-center justify-between">
                                    <div className="space-y-0.5">
                                        <Label>Auto-add new carriers to monitoring</Label>
                                        <p className="text-sm text-muted-foreground">
                                            Automatically add new carriers to Highway monitoring when created
                                        </p>
                                    </div>
                                    <Switch
                                        checked={data.auto_sync_enabled}
                                        onCheckedChange={(checked) => setData('auto_sync_enabled', checked)}
                                    />
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="sync_frequency">Sync Frequency</Label>
                                    <Select 
                                        value={data.sync_frequency} 
                                        onValueChange={(value) => setData('sync_frequency', value)}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select frequency" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="daily">Daily</SelectItem>
                                            <SelectItem value="weekly">Weekly</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <InputError message={errors.sync_frequency} />
                                </div>
                            </div>

                            <div className="flex gap-4">
                                <Button type="submit" disabled={processing}>
                                    {processing ? 'Saving...' : 'Save Configuration'}
                                </Button>
                                <Button type="button" variant="outline" onClick={() => reset()}>
                                    Reset
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AuthenticatedLayout>
    );
}