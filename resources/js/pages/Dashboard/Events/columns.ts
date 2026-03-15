import type { ColumnDef } from "@tanstack/vue-table";
import type { VariantProps } from "class-variance-authority";
import { h } from "vue";
import { Badge } from "@/components/ui/badge";
import type { badgeVariants } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import type { Event, EventStatus } from "@/types";

type BadgeVariants = VariantProps<typeof badgeVariants>;
type StatusConfig = Record<
    EventStatus,
    { variant: BadgeVariants["variant"]; class: string }
>;

const statusConfig: StatusConfig = {
    draft: { variant: "secondary", class: "" },
    published: {
        variant: "default",
        class: "bg-green-600 text-white hover:bg-green-600/90",
    },
    cancelled: { variant: "destructive", class: "" },
};

type Callbacks = {
    onEdit: (event: Event) => void;
    onDelete: (event: Event) => void;
    onPublish: (event: Event) => void;
    onCancel: (event: Event) => void;
};

export function createColumns(callbacks: Callbacks): ColumnDef<Event>[] {
    return [
        {
            accessorKey: "title",
            header: "Title",
            enableSorting: true,
            enableHiding: false,
        },
        {
            header: "Organization",
            enableSorting: false,
            enableHiding: true,
            cell: ({ row }) => row.original.organization?.title ?? "—",
        },
        {
            accessorKey: "starts_at",
            header: "Starts At",
            enableSorting: true,
            enableHiding: true,
            cell: ({ row }) =>
                new Date(row.original.starts_at).toLocaleString(),
        },
        {
            accessorKey: "ends_at",
            header: "Ends At",
            enableSorting: true,
            enableHiding: true,
            cell: ({ row }) => new Date(row.original.ends_at).toLocaleString(),
        },
        {
            accessorKey: "status",
            header: "Status",
            enableSorting: true,
            enableHiding: true,
            cell: ({ row }) => {
                const config = statusConfig[row.original.status];
                return h(
                    Badge,
                    { variant: config.variant, class: config.class },
                    () => row.original.status,
                );
            },
        },
        {
            id: "actions",
            header: "Actions",
            enableSorting: false,
            enableHiding: false,
            cell: ({ row }) => {
                const event = row.original;
                const buttons = [];

                if (event.status === "draft") {
                    buttons.push(
                        h(
                            Button,
                            {
                                variant: "default",
                                size: "sm",
                                onClick: () => callbacks.onPublish(event),
                            },
                            () => "Publish",
                        ),
                    );
                }

                if (event.status === "published") {
                    buttons.push(
                        h(
                            Button,
                            {
                                variant: "outline",
                                size: "sm",
                                onClick: () => callbacks.onCancel(event),
                            },
                            () => "Cancel",
                        ),
                    );
                }

                buttons.push(
                    h(
                        Button,
                        {
                            variant: "outline",
                            size: "sm",
                            onClick: () => callbacks.onEdit(event),
                        },
                        () => "Edit",
                    ),
                    h(
                        Button,
                        {
                            variant: "destructive",
                            size: "sm",
                            onClick: () => callbacks.onDelete(event),
                        },
                        () => "Delete",
                    ),
                );

                return h("div", { class: "flex gap-2" }, buttons);
            },
        },
    ];
}
