import { Link } from "@inertiajs/vue3";
import type { ColumnDef } from "@tanstack/vue-table";
import { h } from "vue";
import UserActions from "@/components/Dashboard/Users/UserActions.vue";
import { Badge } from "@/components/ui/badge";
import type { User } from "@/types/user";
import { show as usersShow } from "@/wayfinder/routes/dashboard/users";

export interface UserColumnActions {
    onEdit: (user: User) => void;
    onDelete: (user: User) => void;
}

export function createUserColumns(actions: UserColumnActions): ColumnDef<User>[] {
    return [
        {
            accessorKey: "name",
            header: "Name",
            enableSorting: true,
            enableHiding: false,
            cell: ({ row }) =>
                h(
                    Link,
                    {
                        href: usersShow({ user: row.original.uuid }),
                        class: "font-medium text-blue-600 hover:underline",
                    },
                    () => row.original.name,
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
                    onEdit: () => actions.onEdit(user),
                    onDelete: () => actions.onDelete(user),
                });
            },
        },
    ];
}
