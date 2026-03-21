import { refDebounced } from "@vueuse/core";
import { ref, shallowRef } from "vue";
import { createOrganizationColumns } from "@/composables/organizations/organizationTableColumns";
import { useDialogState } from "@/composables/useDialogState";
import { useTableState } from "@/composables/useTableState";
import type { PaginatedResponseMeta } from "@/types";
import type { Organization, OrganizationFilterProps, OrganizationStatus } from "@/types/organization";
import { index } from "@/wayfinder/routes/dashboard/organizations";

export function useOrganizationTable(
    pageMeta: PaginatedResponseMeta,
    filters: OrganizationFilterProps,
) {
    const activeOrganization = ref<Organization | null>(null);
    const createOrEditDialog = useDialogState();
    const approveDialog = useDialogState();
    const rejectDialog = useDialogState();
    const deleteDialog = useDialogState();

    const search = shallowRef<OrganizationFilterProps["search"]>(filters.search ?? "");
    const debouncedSearch = refDebounced(search, 300);
    const statusFilter = shallowRef<OrganizationStatus | "">(filters.status ?? "");

    const { isLoading, pagination, sorting } = useTableState(
        pageMeta,
        index(),
        () => ({ search: search.value, status: statusFilter.value }),
        [debouncedSearch, statusFilter],
    );

    const columns = createOrganizationColumns({
        onEdit: (org) => {
            activeOrganization.value = org;
            createOrEditDialog.open();
        },
        onApprove: (org) => {
            activeOrganization.value = org;
            approveDialog.open();
        },
        onReject: (org) => {
            activeOrganization.value = org;
            rejectDialog.open();
        },
        onDelete: (org) => {
            activeOrganization.value = org;
            deleteDialog.open();
        },
    });

    return {
        isLoading,
        createOrEditDialog,
        approveDialog,
        rejectDialog,
        deleteDialog,
        activeOrganization,
        sorting,
        pagination,
        columns,
        search,
        statusFilter,
    };
}
