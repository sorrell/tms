import FileUpload, { FileUploadRef } from '@/Components/FileUpload';
import { TreeDataItem, TreeView } from '@/Components/tree-view';
import { Button } from '@/Components/ui/button';
import { Document, DocumentFolder } from '@/types';
import { useForm } from '@inertiajs/react';
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
} from 'lucide-react';
import { useRef } from 'react';

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
                id: folder.id ?? folder.name,
                name: folder.name,
                icon: Folder,
                openIcon: FolderOpen,
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
        });
    };
    return (
        <div>
            <div>
                <TreeView data={documentData} />
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
        id: doc.id.toString(),
        name: doc.name,
        icon: icon,
        actions: undefined,
        selectedIcon: undefined,
    } as TreeDataItem;
}
