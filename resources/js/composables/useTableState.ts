import { router } from "@inertiajs/vue3";
import type { PaginationState, SortingState } from "@tanstack/vue-table";
import { ref, watch } from "vue";
import type { Ref } from "vue";
import { useTableLoading } from "@/composables/useTableLoading";
import type { PaginatedResponseMeta } from "@/types";

export function useTableState(
    pageMeta: PaginatedResponseMeta,
    indexUrl: string,
    extraParams: () => Record<string, unknown>,
    filterRefs: Ref[],
) {
    const { isLoading, onStart, onFinish } = useTableLoading();
    const pagination = ref<PaginationState>({
        pageIndex: pageMeta.current_page - 1,
        pageSize: pageMeta.per_page,
    });
    const sorting = ref<SortingState>(pageMeta.sort ?? []);

    const handlePagination = () => {
        router.get(
            indexUrl,
            {
                ...extraParams(),
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

    watch(filterRefs, () => {
        pagination.value = { ...pagination.value, pageIndex: 0 };
    });

    watch([pagination, sorting], () => {
        handlePagination();
    });

    return { isLoading, pagination, sorting };
}
