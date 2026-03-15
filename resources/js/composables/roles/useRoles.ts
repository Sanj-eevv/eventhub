import { router } from "@inertiajs/vue3";
import type {
    ColumnDef,
    PaginationState,
    SortingState,
} from "@tanstack/vue-table";
import { refDebounced } from "@vueuse/core";
import { h, ref, watch } from "vue";
import RoleActions from "@/components/Dashboard/Roles/RoleActions.vue";
import { Badge } from "@/components/ui/badge";
import { useDialogState } from "@/composables/useDialogState";
import type { PaginatedResponseMeta } from "@/types";
import type { Role, RoleFilterProps } from "@/types/role";
import {
    edit as rolesEdit,
    index,
    show as rolesShow,
} from "@/wayfinder/routes/dashboard/roles";

export function useRoleTable(
    pageMeta: PaginatedResponseMeta,
    filters: RoleFilterProps,
) {
    const activeRole = ref<Role | null>(null);
    const deleteDialog = useDialogState();

    const search = ref<string>(filters.search ?? "");
    const debouncedSearch = refDebounced(search, 300);

    const isLoading = ref(false);
    const pagination = ref<PaginationState>({
        pageIndex: pageMeta.current_page - 1,
        pageSize: pageMeta.per_page,
    });
    const sorting = ref<SortingState>(pageMeta?.sort ?? []);
    let loadingTimeout: ReturnType<typeof setTimeout> | null = null;

    const handlePagination = () => {
        router.get(
            index(),
            {
                search: search.value,
                sort_by: sorting.value,
                page: pagination.value.pageIndex + 1,
                per_page: pagination.value.pageSize,
            },
            {
                queryStringArrayFormat: "indices",
                preserveState: true,
                replace: true,
                preserveScroll: true,
                onStart: () => {
                    loadingTimeout = setTimeout(() => {
                        isLoading.value = true;
                    }, 250);
                },
                onFinish: () => {
                    if (loadingTimeout) {
                        clearTimeout(loadingTimeout);
                        loadingTimeout = null;
                    }
                    isLoading.value = false;
                },
            },
        );
    };

    watch([debouncedSearch], () => {
        pagination.value = { ...pagination.value, pageIndex: 0 };
    });

    watch([pagination, sorting], () => {
        handlePagination();
    });

    const columns: ColumnDef<Role>[] = [
        {
            accessorKey: "name",
            header: "Name",
            enableSorting: true,
            enableHiding: false,
            cell: ({ row }) =>
                h(
                    "a",
                    {
                        href: rolesShow(row.original).url,
                        class: "font-medium text-blue-600 hover:underline",
                    },
                    row.original.name,
                ),
        },
        {
            accessorKey: "slug",
            header: "Slug",
            enableSorting: true,
            enableHiding: true,
            cell: ({ row }) =>
                h(
                    "span",
                    { class: "font-mono text-sm text-muted-foreground" },
                    row.original.slug,
                ),
        },
        {
            accessorKey: "preserved",
            header: "Type",
            enableSorting: true,
            enableHiding: true,
            cell: ({ row }) =>
                h(
                    Badge,
                    {
                        variant: row.original.preserved
                            ? "secondary"
                            : "outline",
                    },
                    () => (row.original.preserved ? "System" : "Custom"),
                ),
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
                const role = row.original;
                return h(RoleActions, {
                    canEdit: role.slug !== "super-admin",
                    canDelete: !role.preserved,
                    onEdit: () => {
                        router.visit(rolesEdit(role).url);
                    },
                    onDelete: () => {
                        activeRole.value = role;
                        deleteDialog.open();
                    },
                });
            },
        },
    ];

    return {
        isLoading,
        deleteDialog,
        activeRole,
        handlePagination,
        sorting,
        pagination,
        columns,
        search,
    };
}
