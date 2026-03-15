<script setup lang="ts">
import RoleForm from "@/components/Dashboard/Roles/RoleForm.vue";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import type { BreadcrumbItem } from "@/types";
import type { GroupedPermissions, Role } from "@/types/role";
import { index as dashboardIndex } from "@/wayfinder/routes/dashboard";
import { index as rolesIndex } from "@/wayfinder/routes/dashboard/roles";

type RoleEditPageProps = {
    role: Role;
    groupedPermissions: GroupedPermissions;
};
const props = defineProps<RoleEditPageProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: dashboardIndex().url },
    { title: "Roles", href: rolesIndex().url },
    { title: props.role.name },
];
</script>

<template>
    <DashboardLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-[calc(100svh-4rem)] flex-col overflow-hidden group-has-data-[collapsible=icon]/sidebar-wrapper:h-[calc(100svh-3rem)]"
        >
            <div class="shrink-0 border-b px-6 py-5 lg:px-8">
                <h1 class="text-xl font-semibold tracking-tight">
                    {{ role.name }}
                </h1>
                <p class="mt-0.5 text-sm text-muted-foreground">
                    Update the role details and permissions below.
                </p>
            </div>
            <div class="flex min-h-0 flex-1 flex-col">
                <RoleForm
                    :groupedPermissions="props.groupedPermissions"
                    :role="role"
                />
            </div>
        </div>
    </DashboardLayout>
</template>
