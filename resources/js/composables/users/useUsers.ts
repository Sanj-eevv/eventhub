import { router } from "@inertiajs/vue3";
import type {
    ColumnDef,
    PaginationState,
    SortingState,
} from "@tanstack/vue-table";
import { refDebounced } from "@vueuse/core";
import { h, ref, watch } from "vue";
import UserActions from "@/components/Dashboard/Users/UserActions.vue";
import { Badge } from "@/components/ui/badge";
import { useDialogState } from "@/composables/useDialogState";
import type { PaginatedResponseMeta } from "@/types";
import type { User, UserFilterProps } from "@/types/user";
import { index, show as usersShow } from "@/wayfinder/routes/dashboard/users";

export function useUserTable(
    pageMeta: PaginatedResponseMeta,
    filters: UserFilterProps,
) {
    const activeUser = ref<User | null>(null);
    const createOrEditDialog = useDialogState();
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

    const columns: ColumnDef<User>[] = [
        {
            accessorKey: "name",
            header: "Name",
            enableSorting: true,
            enableHiding: false,
            cell: ({ row }) =>
                h(
                    "a",
                    {
                        href: usersShow({ user: row.original.uuid }).url,
                        class: "font-medium text-blue-600 hover:underline",
                    },
                    row.original.name,
                ),
        },
        {
            accessorKey: "email",
            header: "Email",
            enableSorting: true,
            enableHiding: true,
        },
        {
            accessorKey: "role_name",
            header: "Role",
            enableSorting: true,
            enableHiding: true,
            cell: ({ row }) =>
                h(
                    Badge,
                    { variant: "secondary" },
                    () => row.original.role?.name ?? "—",
                ),
        },
        {
            accessorKey: "organization_title",
            header: "Organization",
            enableSorting: true,
            enableHiding: true,
            cell: ({ row }) => row.original.organization?.title ?? "—",
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
                const user = row.original;
                return h(UserActions, {
                    onEdit: () => {
                        activeUser.value = user;
                        createOrEditDialog.open();
                    },
                    onDelete: () => {
                        activeUser.value = user;
                        deleteDialog.open();
                    },
                });
            },
        },
    ];

    return {
        isLoading,
        createOrEditDialog,
        deleteDialog,
        activeUser,
        handlePagination,
        sorting,
        pagination,
        columns,
        search,
    };
}
