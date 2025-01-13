import { Button } from '@/Components/ui/button';
import GuestLayout from '@/Layouts/GuestLayout';
import { cn } from '@/lib/utils';
import { Organization, OrganizationInvite } from '@/types/organization';
import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

export default function InviteAccept({
    invite,
    organization,
}: {
    invite: OrganizationInvite;
    organization: Organization;
}) {
    const { data, setData, post, processing, errors } = useForm({});

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(
            route('organizations.invites.accept', {
                invite: invite.code,
                organization: organization.id,
            }),
        );
    };

    return (
        <GuestLayout>
            <Head title="Accept Organization Invite" />

            <div className={cn('flex flex-col gap-6')}>
                <form onSubmit={submit}>
                    <div className="flex flex-col gap-6">
                        <h1 className="mb-8 text-xl font-bold">
                            Join {organization.name}
                        </h1>
                    </div>
                    <div className="flex flex-col gap-6">
                        <div className="grid gap-2">
                            <p>
                                Invite expires at{' '}
                                {new Date(invite.expire_at).toLocaleString()}{' '}
                            </p>
                        </div>
                        <Button
                            type="submit"
                            className="w-full"
                            disabled={processing}
                            tabIndex={2}
                        >
                            Accept Invite
                        </Button>
                    </div>
                </form>
            </div>
            {status && (
                <div className="mb-4 w-full text-center text-sm font-medium text-green-600">
                    {status}
                </div>
            )}
        </GuestLayout>
    );
}
