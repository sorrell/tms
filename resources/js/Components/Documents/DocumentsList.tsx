import { Document } from "@/types";
import { TreeView } from "@/Components/tree-view";
import { useForm } from "@inertiajs/react";
import FileUpload, { FileUploadRef } from "@/Components/FileUpload";
import { Button } from "@/Components/ui/button";
import { useRef } from "react";
import {
    File,
    FileText,
    FileImage,
    FileVideo,
    FileAudio,
    FileCode,
    FileSpreadsheet,
    FileArchive,
    FileJson
} from "lucide-react";

interface DocumentsListProps {
    documents: Document[];
    documentableType: string;
    documentableId: number;
}

export default function DocumentsList({
    documents,
    documentableType,
    documentableId,
}: DocumentsListProps) {
    const fileUploadRef = useRef<FileUploadRef>(null);

    const documentData = documents.map((doc) => {
        const extension = doc.name.split('.').pop()?.toLowerCase() || '';
        const icon = getDocumentIcon(extension);

        return {
            id: doc.id.toString(),
            name: doc.name,
            icon: icon
        };
    });

    const { data: fileUploadData, setData: setFileUploadData, post } = useForm<{
        file: File | null;
        folder_name: string | null;
    }>({
        file: null,
        folder_name: null,
    });

    const handleFileUpload = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        post(route('documents.store', { documentableType, documentableId }), {
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
            <form onSubmit={handleFileUpload} className="flex flex-col gap-2 w-fit mx-auto">
                <FileUpload
                    ref={fileUploadRef}
                    onFileChange={(file) => setFileUploadData('file', file)}
                    initialPreview={fileUploadData.file?.name}
                />
                <Button disabled={!fileUploadData.file} className="w-fit mx-auto" type="submit">Upload</Button>
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