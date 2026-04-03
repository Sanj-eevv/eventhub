import { refDebounced } from "@vueuse/core";
import { shallowRef } from "vue";
import { createOrderColumns } from "@/composables/orders/orderTableColumns";
import { useTableState } from "@/composables/useTableState";
import type { PaginatedResponseMeta } from "@/types";
import type { OrderFilterProps } from "@/types/order";
import { index } from "@/wayfinder/routes/dashboard/orders";

export function useOrderTable(
    pageMeta: PaginatedResponseMeta,
    filters: OrderFilterProps,
) {
    const search = shallowRef<string>(filters.search ?? "");
    const debouncedSearch = refDebounced(search, 300);

    const { isLoading, pagination, sorting } = useTableState(
        pageMeta,
        index(),
        () => ({ search: search.value }),
        [debouncedSearch],
    );

    const columns = createOrderColumns();

    return { isLoading, sorting, pagination, columns, search };
}
