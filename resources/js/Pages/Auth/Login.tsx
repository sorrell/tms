import { LoginForm } from '@/components/login-form';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

export default function Login({
    status,
    canResetPassword,
}: {
    status?: string;
    canResetPassword: boolean;
}) {
    // const { data, setData, post, processing, errors, reset } = useForm({
    //     email: '',
    //     password: '',
    //     remember: false,
    // });

    // const submit: FormEventHandler = (e) => {
    //     e.preventDefault();

    //     post(route('login'), {
    //         onFinish: () => reset('password'),
    //     });
    // };

    return (
        <GuestLayout>
            <Head title="Log in" />

            {status && (
                <div className="mb-4 text-sm font-medium text-green-600">
                    {status}
                </div>
            )}

            <LoginForm />
        </GuestLayout>
    );
}
