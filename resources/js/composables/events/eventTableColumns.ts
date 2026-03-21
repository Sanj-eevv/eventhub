import { Link } from "@inertiajs/vue3";
import type { ColumnDef } from "@tanstack/vue-table";
import { h } from "vue";
import EventActions from "@/components/Dashboard/Events/EventActions.vue";
import EventStatusBadge from "@/components/Dashboard/Events/EventStatusBadge.vue";
import type { Event } from "@/types/event";
import { show as eventsShow } from "@/wayfinder/routes/dashboard/events";

export interface EventColumnActions {
    onEdit: (event: Event) => void;
    onPublish: (event: Event) => void;
    onCancel: (event: Event) => void;
    onDelete: (event: Event) => void;
}

export function createEventColumns(actions: EventColumnActions): ColumnDef<Event>[] {
    return [
        {
            accessorKey: "title",
            header: "Title",
            enableSorting: true,
            enableHiding: false,
            cell: ({ row }) =>
                h(
                    Link,
                    {
                        href: eventsShow({ event: row.original.uuid }),
                        class: "font-medium text-blue-600 hover:underline",
                    },
                    () => row.original.title,
                ),
        },
        {
            accessorKey: "organization",
            header: "Organization",
            enableSorting: false,
            enableHiding: true,
            cell: ({ row }) => row.original.organization?.title ?? "—",
        },
        {
            accessorKey: "status",
            header: "Status",
            enableSorting: true,
            enableHiding: true,
            cell: ({ row }) => h(EventStatusBadge, { status: row.original.status }),
        },
        {
            accessorKey: "starts_at",
            header: "Starts At",
            enableSorting: true,
            enableHiding: true,
        },
        {
            accessorKey: "ends_at",
            header: "Ends At",
            enableSorting: true,
            enableHiding: true,
        },
        {
            id: "actions",
            header: "Actions",
            enableSorting: false,
            enableHiding: false,
            cell: ({ row }) => {
                const event = row.original;
                return h(EventActions, {
                    event,
                    onEdit: () => actions.onEdit(event),
                    onPublish: () => actions.onPublish(event),
                    onCancel: () => actions.onCancel(event),
                    onDelete: () => actions.onDelete(event),
                });
            },
        },
    ];
}
