import { router } from "@inertiajs/vue3";
import { refDebounced } from "@vueuse/core";
import { ref, shallowRef } from "vue";
import { toast } from "vue-sonner";
import { createEventColumns } from "@/composables/events/eventTableColumns";
import { useDialogState } from "@/composables/useDialogState";
import { useTableState } from "@/composables/useTableState";
import type { PaginatedResponseMeta } from "@/types";
import type { Event, EventFilterProps, EventStatus } from "@/types/event";
import {
    cancel,
    edit as eventsEdit,
    index,
    publish,
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

    const { isLoading, pagination, sorting } = useTableState(
        pageMeta,
        index(),
        () => ({ search: search.value, status: statusFilter.value }),
        [debouncedSearch, statusFilter],
    );

    const columns = createEventColumns({
        onEdit: (event) => router.visit(eventsEdit(event.uuid).url),
        onPublish: (event) =>
            router.post(publish(event.uuid).url, {}, {
                preserveScroll: true,
                onError: () => toast.error("Failed to publish event. Please try again."),
            }),
        onCancel: (event) =>
            router.post(cancel(event.uuid).url, {}, {
                preserveScroll: true,
                onError: () => toast.error("Failed to cancel event. Please try again."),
            }),
        onDelete: (event) => {
            activeEvent.value = event;
            deleteDialog.open();
        },
    });

    return { isLoading, deleteDialog, activeEvent, sorting, pagination, columns, search, statusFilter };
}
