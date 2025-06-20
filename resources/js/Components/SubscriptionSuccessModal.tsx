import { Button } from '@/Components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog';
import { CheckCircle, Sparkles } from 'lucide-react';
import { useEffect, useRef } from 'react';

interface SubscriptionSuccessModalProps {
    isOpen: boolean;
    onClose: () => void;
    subscriptionType: 'premium' | 'startup';
}

interface ConfettiInstance {
    fire: (options: {
        particleCount: number;
        spread: number;
        origin?: { x?: number; y?: number };
        angle?: number;
    }) => Promise<void>;
}

export default function SubscriptionSuccessModal({
    isOpen,
    onClose,
    subscriptionType,
}: SubscriptionSuccessModalProps) {
    const confettiRef = useRef<ConfettiInstance | null>(null);

    useEffect(() => {
        if (isOpen && confettiRef.current) {
            // Fire confetti when modal opens
            const fireConfetti = async () => {
                // Multiple bursts for celebration effect
                if (confettiRef.current) {
                    await confettiRef.current.fire({
                        particleCount: 100,
                        spread: 70,
                        origin: { y: 0.6 },
                    });
                }

                setTimeout(async () => {
                    if (confettiRef.current) {
                        await confettiRef.current.fire({
                            particleCount: 50,
                            angle: 60,
                            spread: 55,
                            origin: { x: 0 },
                        });
                    }
                }, 250);

                setTimeout(async () => {
                    if (confettiRef.current) {
                        await confettiRef.current.fire({
                            particleCount: 50,
                            angle: 120,
                            spread: 55,
                            origin: { x: 1 },
                        });
                    }
                }, 400);
            };

            fireConfetti();
        }
    }, [isOpen]);

    const getContent = () => {
        if (subscriptionType === 'premium') {
            return {
                title: '🎉 Welcome to Premium!',
                description:
                    'Thank you for subscribing! Your account has been upgraded and you now have access to all premium features.',
                features: [
                    'Managed hosting activated',
                    'Priority support enabled',
                    'Advanced analytics unlocked',
                ],
            };
        } else {
            return {
                title: '🚀 Welcome to Startup!',
                description:
                    'Thank you for subscribing! Your account has been upgraded and you now have access to all startup features.',
                features: [
                    'Basic shipment management',
                    'Team collaboration tools',
                    'Basic analytics dashboard',
                ],
            };
        }
    };

    const content = getContent();

    return (
        <>
            <Dialog open={isOpen} onOpenChange={onClose}>
                <DialogContent className="text-center sm:max-w-md">
                    <DialogHeader>
                        <div className="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                            <CheckCircle className="h-8 w-8 text-green-600 dark:text-green-400" />
                        </div>
                        <DialogTitle className="text-2xl font-bold">
                            {content.title}
                        </DialogTitle>
                        <DialogDescription className="text-lg">
                            {content.description}
                        </DialogDescription>
                    </DialogHeader>

                    <div className="py-6">
                        <div className="space-y-3 text-sm text-muted-foreground">
                            {content.features.map((feature, index) => (
                                <div
                                    key={index}
                                    className="flex items-center justify-center gap-2"
                                >
                                    <Sparkles className="h-4 w-4 text-yellow-500" />
                                    <span>{feature}</span>
                                </div>
                            ))}
                        </div>
                    </div>

                    <DialogFooter className="sm:justify-center">
                        <Button onClick={onClose} className="w-full sm:w-auto">
                            Get Started
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </>
    );
}
