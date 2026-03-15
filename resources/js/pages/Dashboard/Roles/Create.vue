<script setup lang="ts">
import RoleForm from "@/components/Dashboard/Roles/RoleForm.vue";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import type { BreadcrumbItem } from "@/types";
import type { GroupedPermissions } from "@/types/role";
import { index as dashboardIndex } from "@/wayfinder/routes/dashboard";
import {
    create as rolesCreate,
    index as rolesIndex,
} from "@/wayfinder/routes/dashboard/roles";

defineProps<{
    groupedPermissions: GroupedPermissions;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: dashboardIndex().url },
    { title: "Roles", href: rolesIndex().url },
    { title: "Create Role", href: rolesCreate().url },
];
</script>

<template>
    <DashboardLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-[calc(100svh-4rem)] flex-col overflow-hidden group-has-data-[collapsible=icon]/sidebar-wrapper:h-[calc(100svh-3rem)]"
        >
            <div class="shrink-0 border-b px-6 py-5 lg:px-8">
                <h1 class="text-xl font-semibold tracking-tight">
                    Create Role
                </h1>
                <p class="mt-0.5 text-sm text-muted-foreground">
                    Fill in the details below to create a new role.
                </p>
            </div>

            <div class="flex min-h-0 flex-1 flex-col">
                <RoleForm :groupedPermissions />
            </div>
        </div>
    </DashboardLayout>
</template>
