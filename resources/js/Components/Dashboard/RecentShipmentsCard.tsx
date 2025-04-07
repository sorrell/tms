import { useEffect, useState } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "../ui/card";
import DashboardCard from "./DashboardCard";
import { Shipment } from "@/types";
import { DataTable } from "../Shipments/ShipmentList/DataTable";
import { columns } from "../Shipments/ShipmentList/Columns";
import { Skeleton } from "../ui/skeleton";

export default function RecentShipmentsCard
    () {
    const [shipments, setShipments] = useState<Shipment[]>([]);
    const [loading, setLoading] = useState<boolean>(true);

    useEffect(() => {
        const fetchRecentShipments = async () => {
            try {
                setLoading(true);
                fetch(route('dashboard.cards.recent-shipments'), {
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                })
                    .then(async response => {
                        setShipments(await response.json());
                        setLoading(false);
                    })
            } catch (error) {
                console.error("Error fetching recent shipments:", error);
            } finally {
                setLoading(false);
            }
        };

        fetchRecentShipments();
    }, [setShipments]);

    return (
        loading ? (
            <Skeleton className="w-full h-[32px]" />
        ): (
        <DashboardCard title = "Recent Loads" cols = { 3 }>
            <DataTable columns = { columns } data = { shipments } />
        </DashboardCard >
        )
    );
}