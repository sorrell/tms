import { Button } from '@/Components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog';
import { Label } from '@/Components/ui/label';
import { Textarea } from '@/Components/ui/textarea';
import { useToast } from '@/hooks/UseToast';
import { router } from '@inertiajs/react';
import { CheckCircle, Send } from 'lucide-react';
import { useState } from 'react';

interface GeneralFeedbackModalProps {
    isOpen: boolean;
    onClose: () => void;
    userEmail: string;
}

export default function GeneralFeedbackModal({
    isOpen,
    onClose,
    userEmail,
}: GeneralFeedbackModalProps) {
    const [message, setMessage] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [errors, setErrors] = useState<{ feedback?: string }>({});
    const { toast } = useToast();

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        if (isSubmitting) return;

        // Reset errors
        setErrors({});

        // Basic validation
        const newErrors: { feedback?: string } = {};
        if (!message) {
            newErrors.feedback = 'Message is required';
        } else if (message.length < 10) {
            newErrors.feedback = 'Message must be at least 10 characters';
        }

        if (Object.keys(newErrors).length > 0) {
            setErrors(newErrors);
            return;
        }

        setIsSubmitting(true);

        router.post(
            route('feedback.submit'),
            {
                email: userEmail,
                feedback: message,
            },
            {
                onSuccess: () => {
                    setMessage('');
                    setErrors({});
                    onClose();

                    // Show success toast
                    toast({
                        title: 'Thank you for your feedback!',
                        description: (
                            <>
                                <CheckCircle
                                    className="mr-2 inline h-4 w-4"
                                    color="green"
                                />
                                We've received your feedback and will review it
                                promptly.
                            </>
                        ),
                    });
                },
                onError: (errors) => {
                    setErrors(errors);
                    toast({
                        title: 'Submission failed',
                        description:
                            'There was an error submitting your feedback. Please try again.',
                        variant: 'destructive',
                    });
                },
                onFinish: () => {
                    setIsSubmitting(false);
                },
            },
        );
    };

    const handleClose = () => {
        if (isSubmitting) return;
        setMessage('');
        setErrors({});
        onClose();
    };

    return (
        <Dialog open={isOpen} onOpenChange={handleClose}>
            <DialogContent className="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Send Feedback</DialogTitle>
                    <DialogDescription>
                        Share your thoughts, report issues, or ask questions.
                        We'd love to hear from you!
                    </DialogDescription>
                </DialogHeader>

                <form onSubmit={handleSubmit}>
                    <div className="grid gap-4 py-4">
                        <div className="grid gap-2">
                            <Label htmlFor="message">Message</Label>
                            <Textarea
                                id="message"
                                placeholder="Tell us what's on your mind - feature requests, bug reports, questions, or general feedback..."
                                value={message}
                                onChange={(e) => setMessage(e.target.value)}
                                disabled={isSubmitting}
                                rows={4}
                                className={
                                    errors.feedback ? 'border-red-500' : ''
                                }
                            />
                            {errors.feedback && (
                                <p className="text-sm text-red-500">
                                    {errors.feedback}
                                </p>
                            )}
                        </div>
                    </div>

                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            onClick={handleClose}
                            disabled={isSubmitting}
                        >
                            Cancel
                        </Button>
                        <Button type="submit" disabled={isSubmitting}>
                            {isSubmitting ? (
                                <>
                                    <div className="mr-2 h-4 w-4 animate-spin rounded-full border-2 border-background border-t-transparent" />
                                    Sending...
                                </>
                            ) : (
                                <>
                                    <Send className="mr-2 h-4 w-4" />
                                    Send Feedback
                                </>
                            )}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
