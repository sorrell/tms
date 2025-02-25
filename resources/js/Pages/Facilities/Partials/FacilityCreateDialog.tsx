import FacilityForm from '@/Components/CreateForms/FacilityForm';
import { Button } from '@/Components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog';
import { Facility } from '@/types';
import { router } from '@inertiajs/react';
import { useRef } from 'react';

export default function FacilityCreateDialog({
    isOpen,
    setIsOpen,
}: {
    isOpen: boolean;
    setIsOpen: (isOpen: boolean) => void;
}) {
    const formRef = useRef<HTMLFormElement>(null);

    const handleCreate = (facility: Facility) => {
        setIsOpen(false);
        router.visit(route('facilities.show', { facility }));
    };

    const handleSubmit = () => {
        formRef.current?.requestSubmit();
    };

    return (
        <Dialog open={isOpen} onOpenChange={setIsOpen}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Create Facility</DialogTitle>
                </DialogHeader>
                <FacilityForm
                    formRef={formRef}
                    onCreate={handleCreate}
                    showFormHeader={false}
                    className="pt-2"
                />
                <DialogFooter className="mt-4">
                    <Button
                        type="button"
                        variant="outline"
                        onClick={() => setIsOpen(false)}
                    >
                        Cancel
                    </Button>
                    <Button type="button" onClick={handleSubmit}>
                        Create
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
