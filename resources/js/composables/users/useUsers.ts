import { refDebounced } from "@vueuse/core";
import { ref, shallowRef } from "vue";
import { createUserColumns } from "@/composables/users/userTableColumns";
import { useDialogState } from "@/composables/useDialogState";
import { useTableState } from "@/composables/useTableState";
import type { PaginatedResponseMeta } from "@/types";
import type { User, UserFilterProps } from "@/types/user";
import { index } from "@/wayfinder/routes/dashboard/users";

export function useUserTable(
    pageMeta: PaginatedResponseMeta,
    filters: UserFilterProps,
) {
    const activeUser = ref<User | null>(null);
    const createOrEditDialog = useDialogState();
    const deleteDialog = useDialogState();

    const search = shallowRef<string>(filters.search ?? "");
    const debouncedSearch = refDebounced(search, 300);

    const { isLoading, pagination, sorting } = useTableState(
        pageMeta,
        index(),
        () => ({ search: search.value }),
        [debouncedSearch],
    );

    const columns = createUserColumns({
        onEdit: (user) => {
            activeUser.value = user;
            createOrEditDialog.open();
        },
        onDelete: (user) => {
            activeUser.value = user;
            deleteDialog.open();
        },
    });

    return { isLoading, createOrEditDialog, deleteDialog, activeUser, sorting, pagination, columns, search };
}
