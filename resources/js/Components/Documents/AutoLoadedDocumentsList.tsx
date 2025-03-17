import { Documentable } from "@/types/enums";
import DocumentsList from "./DocumentsList";
import { useEffect, useState } from "react";
import { Document, DocumentFolder } from "@/types";
import { Skeleton } from "../ui/skeleton";



interface AutoLoadedDocumentsListProps {
    documentableType: Documentable;
    documentableId: number;
}


export default function AutoLoadedDocumentsList({ documentableType, documentableId }: AutoLoadedDocumentsListProps) {

    let documents : Document[] = [];
    let folders : DocumentFolder[] = [];
    const [isLoading, setIsLoading] = useState(false);

    useEffect(() => {
        setIsLoading(true);
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

                console.log(data);

                documents = data.documents;
                folders = data.folders;
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