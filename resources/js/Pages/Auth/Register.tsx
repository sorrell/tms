import ApplicationLogoBox from '@/Components/ApplicationLogoBox';
import InputError from '@/Components/InputError';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler } from 'react';

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm<{
        name: string;
        email: string;
        password: string;
        password_confirmation: string;
        agree_to_terms: boolean;
    }>({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        agree_to_terms: false,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route('register'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    const app = usePage().props.app;

    return (
        <GuestLayout>
            <Head title="Register" />

            <form onSubmit={submit}>
                <div className="flex flex-col gap-6">
                    <div className="flex flex-col items-center gap-2">
                        <a
                            href="#"
                            className="flex flex-col items-center gap-2 font-medium"
                        >
                            <ApplicationLogoBox />
                            <span className="sr-only">{app.name}</span>
                        </a>
                        <h1 className="text-xl font-bold">
                            Sign up for {app.name}
                        </h1>
                        <div className="text-center text-sm">
                            Already have an account?{' '}
                            <a
                                href={route('login')}
                                className="underline underline-offset-4"
                            >
                                Login
                            </a>
                        </div>
                    </div>
                    <div className="flex flex-col gap-6">
                        <div className="grid gap-2">
                            <Label htmlFor="name">Name</Label>

                            <Input
                                id="name"
                                name="name"
                                value={data.name}
                                className="mt-1 block w-full"
                                autoComplete="name"
                                autoFocus={true}
                                onChange={(e) =>
                                    setData('name', e.target.value)
                                }
                                required
                            />

                            <InputError
                                message={errors.name}
                                className="mt-2"
                            />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="email">Email</Label>

                            <Input
                                id="email"
                                type="email"
                                name="email"
                                value={data.email}
                                className="mt-1 block w-full"
                                autoComplete="username"
                                onChange={(e) =>
                                    setData('email', e.target.value)
                                }
                                required
                            />

                            <InputError
                                message={errors.email}
                                className="mt-2"
                            />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="password">Password</Label>

                            <Input
                                id="password"
                                type="password"
                                name="password"
                                value={data.password}
                                className="mt-1 block w-full"
                                autoComplete="new-password"
                                onChange={(e) =>
                                    setData('password', e.target.value)
                                }
                                required
                            />

                            <InputError
                                message={errors.password}
                                className="mt-2"
                            />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="password_confirmation">
                                Confirm Password
                            </Label>

                            <Input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                value={data.password_confirmation}
                                className="mt-1 block w-full"
                                autoComplete="new-password"
                                onChange={(e) =>
                                    setData(
                                        'password_confirmation',
                                        e.target.value,
                                    )
                                }
                                required
                            />

                            <InputError
                                message={errors.password_confirmation}
                                className="mt-2"
                            />
                        </div>

                        <div className="flex items-start gap-2">
                            <input
                                id="agree_to_terms"
                                type="checkbox"
                                name="agree_to_terms"
                                checked={data.agree_to_terms}
                                onChange={(e) =>
                                    setData('agree_to_terms', e.target.checked)
                                }
                                className="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                required
                            />
                            <Label
                                htmlFor="agree_to_terms"
                                className="text-sm leading-relaxed"
                            >
                                I agree to the{' '}
                                <a
                                    href="https://get.loadpartner.io/terms-of-service"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="text-blue-600 underline underline-offset-4 hover:text-blue-800"
                                >
                                    Terms of Service
                                </a>{' '}
                                and{' '}
                                <a
                                    href="https://get.loadpartner.io/privacy-policy"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="text-blue-600 underline underline-offset-4 hover:text-blue-800"
                                >
                                    Privacy Policy
                                </a>
                            </Label>
                        </div>

                        <InputError
                            message={errors.agree_to_terms}
                            className="mt-2"
                        />

                        <div className="flex items-center justify-end">
                            <Button
                                className="w-full"
                                disabled={processing || !data.agree_to_terms}
                            >
                                Register
                            </Button>
                        </div>
                    </div>
                </div>
            </form>
        </GuestLayout>
    );
}
