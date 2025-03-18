import { Documentable } from "@/types/enums";
import DocumentsList from "./DocumentsList";
import { useEffect, useState } from "react";
import { Document, DocumentFolder } from "@/types";
import { Skeleton } from "../ui/skeleton";

interface AutoLoadDocumentsListProps {
    documentableType: Documentable;
    documentableId: number;
}

export default function AutoLoadDocumentsList({ documentableType, documentableId }: AutoLoadDocumentsListProps) {
    const [documents, setDocuments] = useState<Document[]>([]);
    const [folders, setFolders] = useState<DocumentFolder[]>([]);
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        fetch(
            route('documents.index', [documentableType, documentableId]),
            {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }
            }
        ).then((response) => {
            response.json().then((data) => {
                setDocuments(data.documents);
                setFolders(data.folders);
                setIsLoading(false);
            });
        });
    }, [])

    return (<>
        {isLoading ? (
            <Skeleton className="w-full h-[100px]" />
        ) : (
            <DocumentsList
                documents={documents}
                folders={folders}
                documentableId={documentableId}
                documentableType={documentableType}
            />
        )}
    </>
    )
}