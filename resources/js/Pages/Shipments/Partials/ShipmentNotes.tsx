import { Button } from '@/Components/ui/button';
import { Card, CardContent } from '@/Components/ui/card';
import { Skeleton } from '@/Components/ui/skeleton';
import { useToast } from '@/hooks/use-toast';
import { Note } from '@/types';
import { Notable } from '@/types/enums';
import { useForm, usePage } from '@inertiajs/react';
import { Trash } from 'lucide-react';

export default function ShipmentNotes({ shipmentId, notes }: { shipmentId: number, notes: Note[] }) {
    const user = usePage().props.auth.user;


    const { toast } = useToast();
    
    const { post: postNewNote, setData: setNewNoteData, data: newNoteData, errors: newNoteErrors } = useForm({
        note: '',
        notable_type: Notable.Shipment,
        notable_id: shipmentId,
        user_id: user?.id,
    });

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


    return (
        <Card>
            <CardContent className="pt-6">
                <div className="space-y-4">
                    <div className="flex gap-2">
                        <Button>Add Note</Button>
                        <Button disabled={true} variant="outline">Filter</Button>
                    </div>
                    {notes?.map((note) => (
                        <div key={note.id} className="border rounded-lg p-4">
                            <div className="flex justify-between items-center mb-2">
                                <div className="font-medium">{note.user?.name}</div>
                                <div className="text-sm text-gray-500">
                                    {new Date(note.created_at).toLocaleDateString('en-US', {
                                        year: 'numeric',
                                        month: 'short',
                                        day: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    })}
                                </div>
                            </div>
                            <p className="text-gray-700">{note.note}</p>
                            {note.user_id === user?.id && (
                                <div className="flex justify-end">
                                    <Button variant="destructive" size="icon" onClick={() => handleDeleteNote(note.id)}>
                                        <Trash className="w-4 h-4" />
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
