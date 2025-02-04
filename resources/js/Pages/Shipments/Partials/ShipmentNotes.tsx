import InputError from '@/Components/InputError';
import Notes from '@/Components/Notes';
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
import { useToast } from '@/hooks/UseToast';
import { Note } from '@/types';
import { Notable } from '@/types/enums';
import { useForm, usePage } from '@inertiajs/react';
import axios from 'axios';
import { Trash } from 'lucide-react';
import { FormEventHandler, useState, useEffect } from 'react';

export default function ShipmentNotes({
    shipmentId,
}: {
    shipmentId: number;
}) {
    
    return (
        <Card>
            <CardContent className="pt-6">
                <Notes 
                    notableType={Notable.Shipment}
                    notableId={shipmentId}
                />
            </CardContent>
        </Card>
    );
}
