import { Link, router } from "@inertiajs/vue3";
import type {
    ColumnDef,
    PaginationState,
    SortingState,
} from "@tanstack/vue-table";
import { refDebounced } from "@vueuse/core";
import { h, ref, shallowRef, watch } from "vue";
import OrganizationActions from "@/components/Dashboard/Organizations/OrganizationActions.vue";
import OrganizationStatusBadge from "@/components/Dashboard/Organizations/OrganizationStatusBadge.vue";
import { useDialogState } from "@/composables/useDialogState";
import { useTableLoading } from "@/composables/useTableLoading";
import type { PaginatedResponseMeta } from "@/types";
import type { Organization, OrganizationFilterProps } from "@/types/organization";
import { index, show as orgsShow } from "@/wayfinder/routes/dashboard/organizations";

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

    const { isLoading, onStart, onFinish } = useTableLoading();
    const pagination = ref<PaginationState>({
        pageIndex: pageMeta.current_page - 1,
        pageSize: pageMeta.per_page,
    });
    const sorting = ref<SortingState>(pageMeta.sort);

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

    const columns: ColumnDef<Organization>[] = [
        {
            accessorKey: "title",
            header: "Title",
            enableSorting: true,
            enableHiding: false,
            cell: ({ row }) =>
                h(
                    Link,
                    {
                        href: orgsShow({ organization: row.original.uuid }),
                        class: "font-medium text-blue-600 hover:underline",
                    },
                    () => row.original.title,
                ),
        },
        {
            accessorKey: "contact_email",
            header: "Contact Email",
            enableSorting: true,
            enableHiding: true,
        },
        {
            accessorKey: "status",
            header: "Status",
            enableSorting: true,
            enableHiding: true,
            cell: ({ row }) => h(OrganizationStatusBadge, { status: row.original.status }),
        },
        {
            accessorKey: "created_at",
            header: "Created",
            enableSorting: true,
            enableHiding: true,
        },
        {
            id: "actions",
            header: "Actions",
            enableSorting: false,
            enableHiding: false,
            cell: ({ row }) => {
                const org = row.original;
                return h(OrganizationActions, {
                    organization: org,
                    onEdit: () => {
                        activeOrganization.value = org;
                        createOrEditDialog.open();
                    },
                    onApprove: () => {
                        activeOrganization.value = org;
                        approveDialog.open();
                    },
                    onReject: () => {
                        activeOrganization.value = org;
                        rejectDialog.open();
                    },
                    onDelete: () => {
                        activeOrganization.value = org;
                        deleteDialog.open();
                    },
                });
            },
        },
    ];

    return {
        isLoading,
        createOrEditDialog,
        approveDialog,
        rejectDialog,
        deleteDialog,
        activeOrganization,
        handlePagination,
        sorting,
        pagination,
        columns,
        search,
        statusFilter,
    };
}
