import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/Components/ui/card';
import SeatSelectionModal from '@/Components/SeatSelectionModal';
import FeedbackModal from '@/Components/FeedbackModal';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Guest from '@/Layouts/GuestLayout';
import { Head, Link, usePage } from '@inertiajs/react';
import { Check, Github, ArrowRight, Star, Settings } from 'lucide-react';
import { useState } from 'react';

export default function Products({ hasSubscription }: { hasSubscription: boolean }) {
    const [showSeatModal, setShowSeatModal] = useState(false);
    const [showFeedbackModal, setShowFeedbackModal] = useState(false);
    const { auth } = usePage().props as any;

    const plans = [
        {
            name: 'Open Source',
            price: 'Free',
            description: 'Perfect for technical individuals getting started',
            badge: 'Community',
            badgeVariant: 'secondary' as const,
            features: [
                'Full source code access',
                'Community support',
                'Basic features',
                'Self-hosted deployment',
                'Active development'
            ],
            buttonText: 'View on GitHub',
            buttonVariant: 'outline' as const,
            buttonIcon: <Github className="h-4 w-4" />,
            href: 'https://github.com/loadpartner/tms',
            isPopular: false,
            requiresSeatSelection: false
        },
        {
            name: 'Premium',
            price: '$50',
            priceUnit: '/user/month',
            description: 'For individuals and growing teams that want hassle-free management',
            badge: 'Most Popular',
            badgeVariant: 'default' as const,
            features: [
                'Everything in Open Source',
                'Managed hosting',
                'Automatic updates',
                'Priority support',
                'Advanced analytics',
                'Team collaboration tools',
                'API access'
            ],
            buttonText: hasSubscription ? 'Manage Billing' : 'Get Started',
            buttonVariant: hasSubscription ? 'outline' as const : 'default' as const,
            buttonIcon: hasSubscription ? <Settings className="h-4 w-4" /> : <ArrowRight className="h-4 w-4" />,
            href: hasSubscription 
                ? route('organizations.billing', auth?.user?.current_organization_id || 1)
                : route('subscriptions.new'), 
            isPopular: true,
            requiresSeatSelection: !hasSubscription
        },
        {
            name: 'Enterprise',
            price: '$299',
            priceUnit: '/month',
            priceNote: 'starting at',
            description: 'For enterprises wanting dedicated infrastructure and custom development',
            badge: '',
            badgeVariant: 'outline' as const,
            features: [
                'Everything in Managed',
                'Dedicated server infrastructure',
                'Custom integrations',
                'Advanced security features',
                'Dedicated support manager',
                'Custom SLA options',
                'White-label options',
                'Custom development hours included'
            ],
            buttonText: 'Contact Sales',
            buttonVariant: 'outline' as const,
            buttonIcon: <ArrowRight className="h-4 w-4" />,
            href: '#', // This will be handled by the click handler to open feedback modal
            isPopular: false,
            requiresSeatSelection: false,
            opensFeedbackModal: true
        }
    ];

    const handlePlanClick = (plan: typeof plans[0]) => {
        if (plan.opensFeedbackModal) {
            setShowFeedbackModal(true);
        } else if (plan.requiresSeatSelection) {
            setShowSeatModal(true);
        } else {
            // For external links or non-seat-selection plans
            if (plan.href.startsWith('http')) {
                window.open(plan.href, '_blank');
            } else {
                window.location.href = plan.href;
            }
        }
    };

    return (
        <div>
            <Head title="Products" />

            <div className="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                {/* Header */}
                <div className="text-center mb-12">
                    <h1 className="text-4xl font-bold tracking-tight text-foreground mb-4">
                        Choose your subscription plan
                    </h1>
                    <p className="text-lg text-muted-foreground max-w-2xl mx-auto">
                        Select the perfect plan for your needs
                    </p>
                </div>

                {/* Plans Grid */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                    {plans.map((plan, index) => (
                        <Card
                            key={plan.name}
                            className={`relative overflow-hidden flex flex-col h-full ${plan.isPopular
                                    ? 'ring-2 ring-primary shadow-xl scale-105'
                                    : 'hover:shadow-lg transition-shadow'
                                }`}
                        >
                            {plan.isPopular && (
                                <div className="absolute top-0 left-0 right-0 bg-primary text-primary-foreground text-center py-2 text-sm font-medium">
                                    <Star className="inline h-4 w-4 mr-1" />
                                    {plan.badge}
                                </div>
                            )}

                            <CardHeader className={plan.isPopular ? 'pt-12' : ''}>
                                <div className="flex items-center justify-between mb-2 md:mt-6">
                                    <CardTitle className="text-xl font-bold">
                                        {plan.name}
                                    </CardTitle>
                                    {!plan.isPopular && (
                                        <Badge variant={plan.badgeVariant}>
                                            {plan.badge}
                                        </Badge>
                                    )}
                                </div>

                                <div className="mb-4">
                                    <div className="flex items-baseline">
                                        {plan.priceNote && (
                                            <span className="text-sm text-muted-foreground mr-1">
                                                {plan.priceNote}
                                            </span>
                                        )}
                                        <span className="text-3xl font-bold text-foreground">
                                            {plan.price}
                                        </span>
                                        {plan.priceUnit && (
                                            <span className="text-sm text-muted-foreground ml-1">
                                                {plan.priceUnit}
                                            </span>
                                        )}
                                    </div>
                                </div>

                                <p className="text-muted-foreground text-sm">
                                    {plan.description}
                                </p>
                            </CardHeader>

                            <CardContent className="space-y-6 flex-grow">
                                {/* Features List */}
                                <ul className="space-y-3">
                                    {plan.features.map((feature, featureIndex) => (
                                        <li key={featureIndex} className="flex items-start">
                                            <Check className="h-5 w-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" />
                                            <span className="text-sm text-foreground">
                                                {feature}
                                            </span>
                                        </li>
                                    ))}
                                </ul>


                            </CardContent>
                            <CardFooter className="mt-auto">
                                {/* CTA Button */}
                                <div className="pt-4 w-full">
                                    <Button
                                        variant={plan.buttonVariant}
                                        className="w-full"
                                        size="lg"
                                        onClick={() => handlePlanClick(plan)}
                                    >
                                        {plan.buttonIcon}
                                        {plan.buttonText}
                                    </Button>
                                </div>
                            </CardFooter>
                        </Card>
                    ))}
                </div>

                {/* Footer Text */}
                <div className="text-center mt-12 text-sm text-muted-foreground">
                    <p>
                        All plans include regular updates and access to our community.
                        <br />
                        Need a custom solution? <button 
                            onClick={() => setShowFeedbackModal(true)} 
                            className="text-primary hover:underline bg-transparent border-none p-0 cursor-pointer"
                        >
                            Contact us
                        </button> for enterprise options.
                    </p>
                </div>
            </div>

            {/* Seat Selection Modal */}
            <SeatSelectionModal 
                isOpen={showSeatModal} 
                onClose={() => setShowSeatModal(false)} 
            />

            {/* Feedback Modal */}
            <FeedbackModal 
                isOpen={showFeedbackModal} 
                onClose={() => setShowFeedbackModal(false)} 
            />
        </div>
    );
}
