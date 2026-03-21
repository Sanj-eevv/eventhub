import { Link, router } from "@inertiajs/vue3";
import type {
    ColumnDef,
    PaginationState,
    SortingState,
} from "@tanstack/vue-table";
import { refDebounced } from "@vueuse/core";
import { h, ref, shallowRef, watch } from "vue";
import { toast } from "vue-sonner";
import EventActions from "@/components/Dashboard/Events/EventActions.vue";
import EventStatusBadge from "@/components/Dashboard/Events/EventStatusBadge.vue";
import { useDialogState } from "@/composables/useDialogState";
import { useTableLoading } from "@/composables/useTableLoading";
import type { PaginatedResponseMeta } from "@/types";
import type { Event, EventFilterProps, EventStatus } from "@/types/event";
import {
    cancel,
    edit as eventsEdit,
    index,
    publish,
    show as eventsShow,
} from "@/wayfinder/routes/dashboard/events";

export function useEventTable(
    pageMeta: PaginatedResponseMeta,
    filters: EventFilterProps,
) {
    const activeEvent = ref<Event | null>(null);
    const deleteDialog = useDialogState();

    const search = shallowRef<string>(filters.search ?? "");
    const debouncedSearch = refDebounced(search, 300);
    const statusFilter = shallowRef<EventStatus | "">(filters.status ?? "");

    const { isLoading, onStart, onFinish } = useTableLoading();
    const pagination = ref<PaginationState>({
        pageIndex: pageMeta.current_page - 1,
        pageSize: pageMeta.per_page,
    });
    const sorting = ref<SortingState>(pageMeta.sort ?? []);

    const handlePagination = () => {
        router.get(
            index(),
            {
                search: search.value,
                status: statusFilter.value,
                sort_by: sorting.value,
                page: pagination.value.pageIndex + 1,
                per_page: pagination.value.pageSize,
            },
            {
                queryStringArrayFormat: "indices",
                preserveState: true,
                replace: true,
                preserveScroll: true,
                onStart,
                onFinish,
            },
        );
    };

    watch([debouncedSearch, statusFilter], () => {
        pagination.value = { ...pagination.value, pageIndex: 0 };
    });

    watch([pagination, sorting], () => {
        handlePagination();
    });

    const columns: ColumnDef<Event>[] = [
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
            cell: ({ row }) =>
                h(EventStatusBadge, { status: row.original.status }),
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
                    onEdit: () => {
                        router.visit(eventsEdit(event.uuid).url);
                    },
                    onPublish: () => {
                        router.post(publish(event.uuid).url, {}, {
                            preserveScroll: true,
                            onError: () => {
                                toast.error("Failed to publish event. Please try again.");
                            },
                        });
                    },
                    onCancel: () => {
                        router.post(cancel(event.uuid).url, {}, {
                            preserveScroll: true,
                            onError: () => {
                                toast.error("Failed to cancel event. Please try again.");
                            },
                        });
                    },
                    onDelete: () => {
                        activeEvent.value = event;
                        deleteDialog.open();
                    },
                });
            },
        },
    ];

    return {
        isLoading,
        deleteDialog,
        activeEvent,
        handlePagination,
        sorting,
        pagination,
        columns,
        search,
        statusFilter,
    };
}
