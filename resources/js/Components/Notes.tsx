import { useToast } from '@/hooks/use-toast';
import { Note } from '@/types';
import { Notable } from '@/types/enums';
import { useForm, usePage } from '@inertiajs/react';
import axios from 'axios';
import { Trash } from 'lucide-react';
import { FormEventHandler, useEffect, useState } from 'react';
import InputError from './InputError';
import { Button } from './ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from './ui/dialog';
import { Skeleton } from './ui/skeleton';
import { Textarea } from './ui/textarea';

export default function Notes({
    notableType,
    notableId,
    className,
    ...props
}: {
    notableType: Notable;
    notableId: number;
} & React.ComponentPropsWithoutRef<'div'>) {
    const [notes, setNotes] = useState<Note[]>([]);
    const [loading, setLoading] = useState(true);

    const user = usePage().props.auth.user;

    const { toast } = useToast();

    const refreshNotes = () => {
        setLoading(true);
        axios
            .get(route('notes.index', { notableType, notableId }))
            .then((response) => {
                setNotes(response.data);
                setLoading(false);
            })
            .catch((error) => {
                toast({
                    title: 'Error',
                    description: 'Failed to fetch notes',
                    variant: 'destructive',
                });
                setLoading(false);
            });
    };

    useEffect(() => {
        refreshNotes();
    }, [notableType, notableId]);

    const { delete: destroyNote, errors: destroyNoteErrors } = useForm({});
    const handleDeleteNote = (noteId: number) => {
        destroyNote(route('notes.destroy', { note: noteId }), {
            onSuccess: () => {
                toast({
                    description: 'Note deleted successfully',
                });
                refreshNotes();
            },
        });
    };

    const {
        post: postNewNote,
        setData: setNewNoteData,
        data: newNoteData,
        reset: resetNewNote,
        errors: newNoteErrors,
    } = useForm({
        note: '',
        notable_type: notableType,
        notable_id: notableId,
        user_id: user?.id,
    });

    const [dialogOpen, setDialogOpen] = useState(false);

    useEffect(() => {
        // Keep dialog open if there are note errors
        if (Object.keys(newNoteErrors).length > 0) {
            setDialogOpen(true);
        }
    }, [newNoteErrors]);

    const handleNewNote: FormEventHandler = async (e) => {
        e.preventDefault();

        postNewNote(route('notes.store', { notableType, notableId }), {
            onSuccess: () => {
                toast({
                    description: 'Note added successfully',
                });
                refreshNotes();
                resetNewNote();
                setDialogOpen(false);
            },
            onError: () => {
                toast({
                    title: 'Error',
                    description: 'Note failed to add',
                    variant: 'destructive',
                });
            },
        });
    };

    return (
        <div className="space-y-4">
            <div className="flex gap-2">
                <Dialog
                    open={dialogOpen}
                    onOpenChange={setDialogOpen}
                    modal={true}
                >
                    <DialogTrigger asChild>
                        <Button>Add Note</Button>
                    </DialogTrigger>
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Add a new note</DialogTitle>
                            <DialogDescription></DialogDescription>
                        </DialogHeader>
                        <div className="">
                            <Textarea
                                value={newNoteData.note}
                                rows={6}
                                placeholder="Something helpful..."
                                onChange={(e) =>
                                    setNewNoteData('note', e.target.value)
                                }
                            />
                        </div>
                        <div className="pt-2">
                            <InputError message={newNoteErrors.note} />
                        </div>
                        <DialogFooter>
                            <Button onClick={handleNewNote} type="button">
                                Add Note
                            </Button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>
                <Button disabled={true} variant="outline">
                    Filter
                </Button>
            </div>
            {loading ? (
                <div className="flex flex-col gap-2">
                    <Skeleton className="h-10 w-full" />
                    <Skeleton className="h-10 w-full" />
                    <Skeleton className="h-10 w-full" />
                </div>
            ) : notes.length === 0 ? (
                <div className="text-center text-sm text-gray-500">
                    No notes yet
                </div>
            ) : (
                notes?.map((note) => (
                    <div key={note.id} className="rounded-lg border p-4">
                        <div className="mb-2 flex items-center justify-between">
                            <div className="font-medium">{note.user?.name}</div>
                            <div className="text-sm text-gray-500">
                                {new Date(note.created_at).toLocaleDateString(
                                    'en-US',
                                    {
                                        year: 'numeric',
                                        month: 'short',
                                        day: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit',
                                    },
                                )}
                            </div>
                        </div>
                        <p className="text-gray-700">{note.note}</p>
                        {note.user_id === user?.id && (
                            <div className="flex justify-end">
                                <Button
                                    variant="destructive"
                                    size="icon"
                                    onClick={() => handleDeleteNote(note.id)}
                                >
                                    <Trash className="h-4 w-4" />
                                </Button>
                            </div>
                        )}
                    </div>
                ))
            )}
        </div>
    );
}
