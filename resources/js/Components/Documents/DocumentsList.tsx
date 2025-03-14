import FileUpload, { FileUploadRef } from '@/Components/FileUpload';
import { TreeDataItem, TreeView } from '@/Components/tree-view';
import { Button } from '@/Components/ui/button';
import { Document, DocumentFolder } from '@/types';
import { router, useForm } from '@inertiajs/react';
import {
    File,
    FileArchive,
    FileAudio,
    FileCode,
    FileImage,
    FileJson,
    FileSpreadsheet,
    FileText,
    FileVideo,
    Folder,
    FolderOpen,
    Trash2,
} from 'lucide-react';
import { useRef, useState } from 'react';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '../ui/dialog';

interface DocumentsListProps {
    documents: Document[];
    documentableType: string;
    documentableId: number;
    folders?: DocumentFolder[];
}

export default function DocumentsList({
    documents,
    documentableType,
    documentableId,
    folders,
}: DocumentsListProps) {
    const fileUploadRef = useRef<FileUploadRef>(null);

    let remainingDocuments = documents;

    const documentData: TreeDataItem[] =
        folders?.map((folder) => {
            const data = {
                id: `folder-${folder.id ?? folder.name}`,
                name: folder.name,
                icon: Folder,
                openIcon: FolderOpen,
                draggable: folder.id ? true : false,
                droppable: true,
            } as TreeDataItem;

            const folderDocs = remainingDocuments.filter(
                (doc) => doc.folder_name == folder.name,
            );
            data.children = folderDocs.map(documentToTreeDataItem);

            // remove the docs we just put in the folder
            remainingDocuments = remainingDocuments.filter(
                (doc) => doc.folder_name != folder.name,
            );

            if (data.children.length == 0) {
                data.icon = FolderOpen;
                data.children = undefined;
            }

            return data;
        }) ?? [];

    documentData?.push(...remainingDocuments.map(documentToTreeDataItem));

    documentData.push({
        id: 'trash',
        name: 'trashcan',
        icon: Trash2,
        droppable: true,
        draggable: false,
        className: "border-dashed border-2 w-fit rounded text-muted-foreground"

    } as TreeDataItem);

    const {
        data: fileUploadData,
        setData: setFileUploadData,
        post,
    } = useForm<{
        file: File | null;
        folder_name: string | null;
        documentable_type: string;
        documentable_id: number;
    }>({
        file: null,
        folder_name: null,
        documentable_type: documentableType,
        documentable_id: documentableId,
    });

    const handleFileUpload = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        post(route('documents.store'), {
            onSuccess: () => {
                setFileUploadData('file', null);
                // Reset the file upload component using the ref
                if (fileUploadRef.current) {
                    fileUploadRef.current.reset();
                }
            },
            preserveScroll: true,
        });
    };

    const [deleteDocument, setDeleteDocument] = useState<Document | undefined>();
    const [showDeleteFileDialog, setshowDeleteFileDialog] = useState<boolean>(false);

    const confirmDeleteFile = (sourceItem: TreeDataItem) => {

        const sourceId = sourceItem.id.replace(/^(document|folder)-/, '');
        const sourceDocument = documents.find(doc => doc.id.toString() == sourceId);

        if (!sourceDocument) {
            console.error("failed to find document for source item", sourceItem);
            return;
        }

        setDeleteDocument(sourceDocument);
        setshowDeleteFileDialog(true);

    };

    const submitDeleteFile = () => {
        router.delete(
            route('documents.destroy', deleteDocument?.id),
            {
                preserveScroll: true,
                onSuccess: () => {
                    setshowDeleteFileDialog(false);
                }
            }
        );
    };

    const handleDragAndDrop = (
        sourceItem: TreeDataItem,
        targetItem: TreeDataItem,
    ) => {

        if (targetItem.id == 'trash') {
            confirmDeleteFile(sourceItem);
            return;
        }

        const targetType = targetItem.id.startsWith('document-')
            ? 'document'
            : 'folder';
        const targetId = targetItem.id.replace(/^(document|folder)-/, '');

        let targetFolder = '';
        switch (targetType) {
            case 'document':
                targetFolder =
                    documents.find((doc) => doc.id.toString() == targetId)
                        ?.folder_name ?? '';
                break;
            case 'folder':
                // TODO - this might break in the future if the name
                // gets changed for front end showing or something
                targetFolder = targetId;
                break;
            default:
                console.error('Failed to find a folder');
        }
        const sourceId = sourceItem.id.replace(/^(document|folder)-/, '');

        // Make sure data is properly set before sending the request
        const dataToSend = {
            folder_name: targetFolder,
        };

        router.put(route('documents.update', sourceId), dataToSend, {
            onError: (e) => {
                console.error('Error', e);
            },
            preserveScroll: true,
        });
    };

    return (
        <div>
            <div>
                <TreeView
                    data={documentData}
                    onDocumentDrag={handleDragAndDrop}
                />
            </div>
            <form
                onSubmit={handleFileUpload}
                className="mx-auto flex w-fit flex-col gap-2"
            >
                <FileUpload
                    ref={fileUploadRef}
                    onFileChange={(file) => setFileUploadData('file', file)}
                    initialPreview={fileUploadData.file?.name}
                />
                <Button
                    disabled={!fileUploadData.file}
                    className="mx-auto w-fit"
                    type="submit"
                >
                    Upload
                </Button>
            </form>
            <Dialog open={showDeleteFileDialog} onOpenChange={setshowDeleteFileDialog}>
            <DialogContent>
                <DialogTitle>
                    Delete Document?
                </DialogTitle>
                <div>
                    <span className="p-1 font-bold">{deleteDocument?.name}</span> will be permanently deleted
                </div>
                <DialogFooter>
                    <Button variant={'ghost'} onClick={() => {setshowDeleteFileDialog(false)}} >Cancel</Button>
                    <Button variant={'destructive'} onClick={submitDeleteFile}>Delete</Button>
                </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    );
}

function getDocumentIcon(extension: string) {
    switch (extension) {
        // Document types
        case 'pdf':
        case 'doc':
        case 'docx':
        case 'txt':
        case 'rtf':
            return FileText;

        // Spreadsheet types
        case 'xls':
        case 'xlsx':
        case 'csv':
            return FileSpreadsheet;

        // Image types
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
        case 'svg':
        case 'webp':
            return FileImage;

        // Video types
        case 'mp4':
        case 'avi':
        case 'mov':
        case 'wmv':
        case 'webm':
            return FileVideo;

        // Audio types
        case 'mp3':
        case 'wav':
        case 'ogg':
        case 'flac':
            return FileAudio;

        // Code types
        case 'html':
        case 'css':
        case 'js':
        case 'ts':
        case 'jsx':
        case 'tsx':
        case 'php':
        case 'py':
            return FileCode;

        // Data types
        case 'json':
        case 'xml':
            return FileJson;

        // Archive types
        case 'zip':
        case 'rar':
        case 'tar':
        case 'gz':
            return FileArchive;

        // Default file icon for unknown types
        default:
            return File;
    }
}

function documentToTreeDataItem(doc: Document) {
    const extension = doc.name.split('.').pop()?.toLowerCase() || '';
    const icon = getDocumentIcon(extension);

    return {
        id: `document-${doc.id}`,
        name: doc.name,
        icon: icon,
        actions: undefined,
        selectedIcon: undefined,
        draggable: true,
        droppable: false,
    } as TreeDataItem;
}
