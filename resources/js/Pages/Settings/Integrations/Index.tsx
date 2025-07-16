import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Organization } from '@/types/organization';
import { Head, Link } from '@inertiajs/react';
import { Settings, CheckCircle, AlertCircle, XCircle } from 'lucide-react';

interface IntegrationStatus {
    name: string;
    slug: string;
    description: string;
    status: 'configured' | 'not_configured' | 'error';
    configureUrl: string;
    details?: string;
}

export default function IntegrationsIndex({
    organization,
    integrations,
}: {
    organization: Organization;
    integrations: IntegrationStatus[];
}) {
    const getStatusIcon = (status: string) => {
        switch (status) {
            case 'configured':
                return <CheckCircle className="h-5 w-5 text-green-500" />;
            case 'error':
                return <AlertCircle className="h-5 w-5 text-red-500" />;
            default:
                return <XCircle className="h-5 w-5 text-gray-400" />;
        }
    };

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'configured':
                return <Badge variant="outline" className="text-green-600 border-green-600">Configured</Badge>;
            case 'error':
                return <Badge variant="destructive">Error</Badge>;
            default:
                return <Badge variant="secondary">Not Configured</Badge>;
        }
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { title: 'Settings' },
                { title: 'Integrations' },
            ]}
        >
            <Head title="Integrations" />
            
            <div className="mx-4 mb-6">
                <h1 className="text-2xl font-bold mb-2">Integrations</h1>
                <p className="text-muted-foreground">
                    Connect LoadPartner TMS with external services to enhance your workflow.
                </p>
            </div>

            <div className="mx-4 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                {integrations.map((integration) => (
                    <Card key={integration.slug} className="relative">
                        <CardHeader className="pb-3">
                            <div className="flex items-center justify-between">
                                <CardTitle className="flex items-center gap-2">
                                    {getStatusIcon(integration.status)}
                                    {integration.name}
                                </CardTitle>
                                {getStatusBadge(integration.status)}
                            </div>
                            <CardDescription>
                                {integration.description}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            {integration.details && (
                                <p className="text-sm text-muted-foreground mb-4">
                                    {integration.details}
                                </p>
                            )}
                            <Link href={integration.configureUrl}>
                                <Button className="w-full">
                                    <Settings className="mr-2 h-4 w-4" />
                                    {integration.status === 'configured' ? 'Manage' : 'Configure'}
                                </Button>
                            </Link>
                        </CardContent>
                    </Card>
                ))}
            </div>
        </AuthenticatedLayout>
    );
}