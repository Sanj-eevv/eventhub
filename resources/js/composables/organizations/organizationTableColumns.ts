import { Link } from "@inertiajs/vue3";
import type { ColumnDef } from "@tanstack/vue-table";
import { h } from "vue";
import OrganizationActions from "@/components/Dashboard/Organizations/OrganizationActions.vue";
import OrganizationStatusBadge from "@/components/Dashboard/Organizations/OrganizationStatusBadge.vue";
import { formatDate } from "@/lib/utils";
import type { Organization } from "@/types/organization";
import { show as orgsShow } from "@/wayfinder/routes/dashboard/organizations";

export interface OrganizationColumnActions {
    onEdit: (org: Organization) => void;
    onApprove: (org: Organization) => void;
    onReject: (org: Organization) => void;
    onDelete: (org: Organization) => void;
}

export function createOrganizationColumns(
    actions: OrganizationColumnActions,
): ColumnDef<Organization>[] {
    return [
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
            cell: ({ row }) =>
                h(OrganizationStatusBadge, { status: row.original.status }),
        },
        {
            accessorKey: "created_at",
            header: "Created",
            enableSorting: true,
            enableHiding: true,
            cell: ({ row }) => formatDate(row.original.created_at),
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
                    onEdit: () => actions.onEdit(org),
                    onApprove: () => actions.onApprove(org),
                    onReject: () => actions.onReject(org),
                    onDelete: () => actions.onDelete(org),
                });
            },
        },
    ];
}
