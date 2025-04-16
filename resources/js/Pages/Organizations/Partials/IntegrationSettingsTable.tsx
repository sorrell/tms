import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import { Button } from '@/Components/ui/button';
import { Checkbox } from '@/Components/ui/checkbox';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu';
import { Input } from '@/Components/ui/input';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import { useToast } from '@/hooks/UseToast';
import { GlobalIntegrationSetting, IntegrationSetting, Organization } from '@/types/organization';
import { useForm } from '@inertiajs/react';
import { Eye, EyeOff, MoreHorizontal } from 'lucide-react';
import { useCallback, useState } from 'react';

interface Props {
    integrationSettings: IntegrationSetting[];
    organization: Organization;
    globalIntegrationSettings: GlobalIntegrationSetting[];
}

interface IntegrationSettingForm {
    key: string;
    value: string;
    provider?: string;
    encrypted: boolean;
    expose_to_frontend: boolean;
    [key: string]: string | boolean | undefined;
}

export default function IntegrationSettingsTable({
    integrationSettings,
    organization,
    globalIntegrationSettings,
}: Props) {
    const { toast } = useToast();
    const {
        data,
        setData,
        post,
        processing,
        errors,
        reset,
        setError,
        delete: destroy,
    } = useForm<IntegrationSettingForm>({
        key: '',
        value: '',
        provider: '',
        encrypted: false,
        expose_to_frontend: false,
    });
    const [open, setOpen] = useState(false);
    const [revealedId, setRevealedId] = useState<number | null>(null);
    const [activeSetting, setActiveSetting] =
        useState<IntegrationSetting | null>(null);

    const handleAddOrEdit = useCallback(
        (e: React.FormEvent) => {
            e.preventDefault();

            // If adding, check for duplicate key
            if (!activeSetting) {
                const keyExists = integrationSettings.some(
                    (setting) => setting.key === data.key,
                );
                if (keyExists) {
                    setError(
                        'key',
                        'This integration setting key already exists.',
                    );
                    return;
                }
            }

            post(
                route('organizations.integration-settings.store', {
                    organization: organization.id,
                }),
                {
                    preserveScroll: true,
                    replace: true,
                    onSuccess: () => {
                        reset();
                        setOpen(false);
                        setActiveSetting(null);
                        toast({
                            description: activeSetting
                                ? 'Integration setting updated!'
                                : 'Integration setting added!',
                        });
                    },
                },
            );
        },
        [
            integrationSettings,
            data,
            reset,
            toast,
            setOpen,
            activeSetting,
            setError,
            post,
            organization.id,
        ],
    );

    const handleEdit = (setting: IntegrationSetting) => {
        setActiveSetting(setting);
        setData({
            key: setting.key,
            value: setting.value,
            provider: setting.provider || '',
            encrypted: setting.encrypted,
            expose_to_frontend: setting.expose_to_frontend,
        });
        setOpen(true);
    };

    const handleDelete = (setting: IntegrationSetting) => {
        destroy(
            route('organizations.integration-settings.destroy', {
                organization: organization.id,
                setting: setting.id,
            }),
            {
                preserveScroll: true,
                replace: true,
                onSuccess: () => {
                    toast({ description: 'Integration setting deleted!' });
                },
                onError: () => {
                    toast({
                        description: 'Failed to delete integration setting',
                        variant: 'destructive',
                    });
                },
            },
        );
    };

    return (
        <div>
            <Button
                onClick={() => {
                    setOpen(true);
                    setActiveSetting(null);
                    setData({
                        key: '',
                        value: '',
                        provider: '',
                        encrypted: false,
                        expose_to_frontend: false,
                    });
                }}
            >
                Add Integration Setting
            </Button>
            <Dialog
                open={open}
                onOpenChange={(val) => {
                    setOpen(val);
                    if (!val) {
                        setActiveSetting(null);
                        reset();
                    }
                }}
            >
                <DialogContent>
                    <form onSubmit={handleAddOrEdit}>
                        <DialogHeader>
                            <DialogTitle className="mb-4">
                                {activeSetting ? 'Edit' : 'Add'} Integration
                                Setting
                            </DialogTitle>
                            <div className="flex flex-col gap-4">
                                <Input
                                    name="key"
                                    placeholder="Key"
                                    value={data.key}
                                    onChange={(e) =>
                                        setData('key', e.target.value)
                                    }
                                    required
                                    disabled={!!activeSetting}
                                />
                                <InputError message={errors.key} />
                                <Input
                                    name="value"
                                    placeholder="Value"
                                    value={data.value}
                                    onChange={(e) =>
                                        setData('value', e.target.value)
                                    }
                                    required
                                />
                                <InputError message={errors.value} />
                                <Input
                                    name="provider"
                                    placeholder="Provider"
                                    value={data.provider}
                                    onChange={(e) =>
                                        setData('provider', e.target.value)
                                    }
                                />
                                <InputError message={errors.provider} />
                                <label className="flex items-center gap-2">
                                    <Checkbox
                                        name="encrypted"
                                        checked={data.encrypted}
                                        onCheckedChange={(checked) =>
                                            setData('encrypted', !!checked)
                                        }
                                    />
                                    Encrypted
                                </label>
                                <InputError message={errors.encrypted} />
                                <label className="flex items-center gap-2">
                                    <Checkbox
                                        name="expose_to_frontend"
                                        checked={data.expose_to_frontend}
                                        onCheckedChange={(checked) =>
                                            setData(
                                                'expose_to_frontend',
                                                !!checked,
                                            )
                                        }
                                    />
                                    Expose to Frontend
                                </label>
                                <InputError
                                    message={errors.expose_to_frontend}
                                />
                            </div>
                        </DialogHeader>
                        <DialogFooter>
                            <Button disabled={processing} type="submit">
                                Save
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
            <Table>
                <TableCaption>A list of integration settings.</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead>Key</TableHead>
                        <TableHead>Value</TableHead>
                        <TableHead>Provider</TableHead>
                        <TableHead>Encrypted</TableHead>
                        <TableHead>Expose to Frontend</TableHead>
                        <TableHead className="text-right">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {integrationSettings.map((setting) => (
                        <TableRow key={setting.id}>
                            <TableCell>{setting.key}</TableCell>
                            <TableCell>
                                {setting.encrypted ? (
                                    <span className="italic text-gray-400">
                                        ••••••••
                                    </span>
                                ) : (
                                    <span className="flex items-center gap-2">
                                        {revealedId === setting.id ? (
                                            <span>{setting.value}</span>
                                        ) : (
                                            <span className="italic text-gray-400">
                                                ••••••••
                                            </span>
                                        )}
                                        <Button
                                            size="sm"
                                            variant="ghost"
                                            type="button"
                                            onClick={() =>
                                                setRevealedId(
                                                    revealedId === setting.id
                                                        ? null
                                                        : setting.id,
                                                )
                                            }
                                        >
                                            {revealedId === setting.id ? (
                                                <EyeOff />
                                            ) : (
                                                <Eye />
                                            )}
                                        </Button>
                                    </span>
                                )}
                            </TableCell>
                            <TableCell>{setting.provider}</TableCell>
                            <TableCell>
                                <Checkbox
                                    checked={setting.encrypted}
                                    disabled
                                />
                            </TableCell>
                            <TableCell>
                                <Checkbox
                                    checked={setting.expose_to_frontend}
                                    disabled
                                />
                            </TableCell>
                            <TableCell className="flex justify-end space-x-4">
                                <PrimaryButton
                                    onClick={() => handleEdit(setting)}
                                >
                                    Edit
                                </PrimaryButton>
                                <DropdownMenu>
                                    <DropdownMenuTrigger asChild>
                                        <Button
                                            variant="ghost"
                                            className="h-8 w-8 p-0"
                                        >
                                            <span className="sr-only">
                                                Open menu
                                            </span>
                                            <MoreHorizontal className="h-4 w-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuLabel>
                                            Actions
                                        </DropdownMenuLabel>
                                        <DropdownMenuItem
                                            onClick={() =>
                                                handleDelete(setting)
                                            }
                                        >
                                            Delete Setting
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
        </div>
    );
}
