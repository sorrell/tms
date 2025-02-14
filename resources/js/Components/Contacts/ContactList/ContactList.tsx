import InputError from '@/Components/InputError';
import { Button } from '@/Components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Skeleton } from '@/Components/ui/skeleton';
import { useToast } from '@/hooks/UseToast';
import { Contact } from '@/types';
import { useForm } from '@inertiajs/react';
import axios from 'axios';
import { Plus, Search } from 'lucide-react';
import { useCallback, useEffect, useRef, useState } from 'react';
import { columns } from './Columns';
import { DataTable } from './DataTable';

export default function ContactList({
    contactForId,
    contactForType,
}: {
    contactForId: number;
    contactForType: string;
}) {
    const [data, setData] = useState<Contact[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [searchTerm, setSearchTerm] = useState('');
    const inputRef = useRef<HTMLInputElement>(null);
    const [isAddContactDialogOpen, setIsAddContactDialogOpen] = useState(false);

    const { toast } = useToast();

    const {
        data: newContactFormData,
        setData: setNewContactFormData,
        post: createNewContact,
        reset: resetNewContactForm,
        errors: newContactFormErrors,
    } = useForm<{
        title?: string;
        name: string;
        email?: string;
        mobile_phone?: string;
        office_phone?: string;
        office_phone_extension?: string;
        contact_for_id: number;
        contact_for_type: string;
    }>({
        title: '',
        name: '',
        email: '',
        mobile_phone: '',
        office_phone: '',
        office_phone_extension: '',
        contact_for_id: contactForId,
        contact_for_type: contactForType,
    });

    const onDeleteContact = useCallback((contact: Contact) => {
        axios
            .delete(route('contacts.destroy', contact.id))
            .then(() => {
                getContacts();
                toast({
                    variant: 'destructive',
                    title: 'Contact deleted successfully',
                });
            })
            .catch(() => {
                console.error('Error deleting contact');
            });
    }, []);

    const getContacts = useCallback((searchTerm?: string) => {
        const getData = (): Promise<Contact[]> => {
            return axios
                .get(route('contacts.search'), {
                    params: {
                        query: searchTerm,
                        with: [],
                        filters: [
                            {
                                name: 'contact_for_id',
                                value: contactForId,
                            },
                            {
                                name: 'contact_for_type',
                                value: contactForType,
                            },
                        ],
                    },
                })
                .then((response) => response.data);
        };

        setIsLoading(true);

        getData()
            .then((contacts) => {
                setData(
                    contacts.map((contact) => ({
                        ...contact,
                        onDelete: onDeleteContact,
                    })),
                );
                setIsLoading(false);
            })
            .catch((error) => {
                console.error('Error fetching contacts:', error);
                setIsLoading(false);
            });
    }, []);

    useEffect(() => {
        if (!isLoading) {
            inputRef.current?.focus();
        }
    }, [isLoading]);

    useEffect(() => {
        getContacts();
    }, [getContacts]);

    return (
        <div className="mx-auto flex w-full max-w-screen-2xl flex-col gap-2">
            <div className="flex justify-between">
                <div className="flex gap-2">
                    <Input
                        ref={inputRef}
                        className="min-w-96 max-w-md"
                        placeholder="Search contacts"
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                        onKeyDown={(e) => {
                            if (e.key === 'Enter') {
                                getContacts(searchTerm);
                            }
                        }}
                    />
                    <Button onClick={() => getContacts(searchTerm)}>
                        <Search className="h-4 w-4" />
                    </Button>
                </div>
                <div>
                    <Button
                        onClick={() => {
                            resetNewContactForm();
                            setIsAddContactDialogOpen(true);
                        }}
                    >
                        <Plus className="h-4 w-4" />
                        Add Contact
                    </Button>
                </div>
            </div>
            {isLoading ? (
                <>
                    <Skeleton className="mx-auto h-[200px] w-1/2 rounded-md" />
                </>
            ) : (
                <>
                    <DataTable columns={columns} data={data} />
                </>
            )}
            <Dialog
                open={isAddContactDialogOpen}
                onOpenChange={setIsAddContactDialogOpen}
            >
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Add Contact</DialogTitle>
                    </DialogHeader>
                    <div className="grid gap-4 py-4">
                        <div>
                            <Label htmlFor="title">Title</Label>
                            <Input
                                id="title"
                                value={newContactFormData.title}
                                onChange={(e) =>
                                    setNewContactFormData(
                                        'title',
                                        e.target.value,
                                    )
                                }
                            />
                            {newContactFormErrors.title && (
                                <InputError
                                    message={newContactFormErrors.title}
                                />
                            )}
                        </div>
                        <div>
                            <Label htmlFor="name">Name</Label>
                            <Input
                                id="name"
                                value={newContactFormData.name}
                                onChange={(e) =>
                                    setNewContactFormData(
                                        'name',
                                        e.target.value,
                                    )
                                }
                            />
                            {newContactFormErrors.name && (
                                <InputError
                                    message={newContactFormErrors.name}
                                />
                            )}
                        </div>
                        <div>
                            <Label htmlFor="email">Email</Label>
                            <Input
                                id="email"
                                value={newContactFormData.email}
                                onChange={(e) =>
                                    setNewContactFormData(
                                        'email',
                                        e.target.value,
                                    )
                                }
                            />
                            {newContactFormErrors.email && (
                                <InputError
                                    message={newContactFormErrors.email}
                                />
                            )}
                        </div>
                        <div>
                            <Label htmlFor="mobile_phone">Mobile Phone</Label>
                            <Input
                                id="mobile_phone"
                                value={newContactFormData.mobile_phone}
                                onChange={(e) =>
                                    setNewContactFormData(
                                        'mobile_phone',
                                        e.target.value,
                                    )
                                }
                            />
                            {newContactFormErrors.mobile_phone && (
                                <InputError
                                    message={newContactFormErrors.mobile_phone}
                                />
                            )}
                        </div>
                        <div className="grid grid-cols-3 gap-4">
                            <div className="col-span-2">
                                <Label htmlFor="office_phone">
                                    Office Phone
                                </Label>
                                <Input
                                    id="office_phone"
                                    value={newContactFormData.office_phone}
                                    onChange={(e) =>
                                        setNewContactFormData(
                                            'office_phone',
                                            e.target.value,
                                        )
                                    }
                                />
                                {newContactFormErrors.office_phone && (
                                    <InputError
                                        message={
                                            newContactFormErrors.office_phone
                                        }
                                    />
                                )}
                            </div>
                            <div>
                                <Label htmlFor="office_phone_extension">
                                    Extension
                                </Label>
                                <Input
                                    id="office_phone_extension"
                                    value={
                                        newContactFormData.office_phone_extension
                                    }
                                    onChange={(e) =>
                                        setNewContactFormData(
                                            'office_phone_extension',
                                            e.target.value,
                                        )
                                    }
                                />
                                {newContactFormErrors.office_phone_extension && (
                                    <InputError
                                        message={
                                            newContactFormErrors.office_phone_extension
                                        }
                                    />
                                )}
                            </div>
                        </div>
                    </div>
                    <DialogFooter>
                        <Button
                            variant="outline"
                            onClick={() => setIsAddContactDialogOpen(false)}
                        >
                            Cancel
                        </Button>
                        <Button
                            onClick={() => {
                                createNewContact(route('contacts.store'), {
                                    onSuccess: () => {
                                        setIsAddContactDialogOpen(false);
                                        getContacts();
                                    },
                                    onError: () => {
                                        console.error('Error creating contact');
                                    },
                                });
                            }}
                        >
                            Add Contact
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    );
}
