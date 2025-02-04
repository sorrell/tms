import InputError from '@/Components/InputError';
import { Button } from '@/Components/ui/button';
import { Card, CardContent } from '@/Components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/Components/ui/dialog';
import { Textarea } from '@/Components/ui/textarea';
import { useToast } from '@/hooks/use-toast';
import { Note } from '@/types';
import { Notable } from '@/types/enums';
import { useForm, usePage } from '@inertiajs/react';
import axios from 'axios';
import { Trash } from 'lucide-react';
import { FormEventHandler, useState, useEffect } from 'react';

export default function ShipmentNotes({
    shipmentId,
    notes,
}: {
    shipmentId: number;
    notes: Note[];
}) {
    const user = usePage().props.auth.user;

    const { toast } = useToast();

    
    const { delete: destroyNote, errors: destroyNoteErrors } = useForm({});
    const handleDeleteNote = (noteId: number) => {
        destroyNote(route('notes.destroy', { note: noteId }), {
            onSuccess: () => {
                toast({
                    description: 'Note deleted successfully',
                });
            },
        });
    };

    const {
        post: postNewNote,
        setData: setNewNoteData,
        data: newNoteData,
        reset: resetNewNote,
        errors: newNoteErrors,
        setError: setNewNoteErrors,
    } = useForm({
        note: '',
        notable_type: Notable.Shipment,
        notable_id: shipmentId,
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
        
        try {
            await axios.post(route('notes.store'), {
                note: newNoteData.note,
                notable_type: Notable.Shipment,
                notable_id: shipmentId,
                user_id: user?.id,
            });
            
            // Manually refresh the notes using Inertia visit
            window.location.reload();
            
            toast({
                description: 'Note added successfully',
            });
            setDialogOpen(false);
            resetNewNote();
        } catch (error: any) {
            toast({
                title: 'Error',
                description: 'Note failed to add',
                variant: 'destructive',
            });
            
            if (error.response?.data?.errors) {
                setNewNoteErrors(error.response.data.errors);
            }
            
            setDialogOpen(true);
        }
    };

    return (
        <Card>
            <CardContent className="pt-6">
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
                                    <Button
                                        onClick={handleNewNote}
                                        type="button"
                                    >
                                        Add Note
                                    </Button>
                                </DialogFooter>
                            </DialogContent>
                        </Dialog>
                        <Button disabled={true} variant="outline">
                            Filter
                        </Button>
                    </div>
                    {notes?.map((note) => (
                        <div key={note.id} className="rounded-lg border p-4">
                            <div className="mb-2 flex items-center justify-between">
                                <div className="font-medium">
                                    {note.user?.name}
                                </div>
                                <div className="text-sm text-gray-500">
                                    {new Date(
                                        note.created_at,
                                    ).toLocaleDateString('en-US', {
                                        year: 'numeric',
                                        month: 'short',
                                        day: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit',
                                    })}
                                </div>
                            </div>
                            <p className="text-gray-700">{note.note}</p>
                            {note.user_id === user?.id && (
                                <div className="flex justify-end">
                                    <Button
                                        variant="destructive"
                                        size="icon"
                                        onClick={() =>
                                            handleDeleteNote(note.id)
                                        }
                                    >
                                        <Trash className="h-4 w-4" />
                                    </Button>
                                </div>
                            )}
                        </div>
                    ))}
                </div>
            </CardContent>
        </Card>
    );
}
