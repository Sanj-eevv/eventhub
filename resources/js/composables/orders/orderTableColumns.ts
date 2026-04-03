import { Link } from "@inertiajs/vue3";
import type { ColumnDef } from "@tanstack/vue-table";
import { h } from "vue";
import { Badge } from "@/components/ui/badge";
import { formatCurrency, formatDate } from "@/lib/utils";
import type { OrderIndexItem } from "@/types/order";
import { show as ordersShow } from "@/wayfinder/routes/dashboard/orders";

const statusVariantMap: Record<
    string,
    "default" | "secondary" | "destructive" | "outline"
> = {
    paid: "default",
    reserved: "secondary",
    cancelled: "destructive",
};

export function createOrderColumns(): ColumnDef<OrderIndexItem>[] {
    return [
        {
            accessorKey: "event",
            header: "Event",
            enableSorting: false,
            enableHiding: false,
            cell: ({ row }) =>
                h(
                    Link,
                    {
                        href: ordersShow({ order: row.original.uuid }),
                        class: "font-medium text-blue-600 hover:underline",
                    },
                    () => row.original.event?.title ?? "—",
                ),
        },
        {
            accessorKey: "user",
            header: "Customer",
            enableSorting: false,
            enableHiding: true,
            cell: ({ row }) => {
                const user = row.original.user;
                return user ? h("div", [
                    h("p", { class: "font-medium" }, user.name),
                    h("p", { class: "text-xs text-muted-foreground" }, user.email),
                ]) : "—";
            },
        },
        {
            accessorKey: "status",
            header: "Status",
            enableSorting: false,
            enableHiding: true,
            cell: ({ row }) =>
                h(
                    Badge,
                    { variant: statusVariantMap[row.original.status.value] ?? "outline" },
                    () => row.original.status.label,
                ),
        },
        {
            accessorKey: "total",
            header: "Total",
            enableSorting: true,
            enableHiding: true,
            cell: ({ row }) => formatCurrency(row.original.total),
        },
        {
            accessorKey: "paid_at",
            header: "Paid",
            enableSorting: true,
            enableHiding: true,
            cell: ({ row }) => row.original.paid_at ? formatDate(row.original.paid_at) : "—",
        },
        {
            accessorKey: "created_at",
            header: "Created",
            enableSorting: true,
            enableHiding: true,
            cell: ({ row }) => formatDate(row.original.created_at),
        },
    ];
}
