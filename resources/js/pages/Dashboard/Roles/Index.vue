<script setup lang="ts">
import { Head, router } from "@inertiajs/vue3";
import { XIcon } from "lucide-vue-next";
import DeleteRoleDialog from "@/components/Dashboard/Roles/DeleteRoleDialog.vue";
import DataTable from "@/components/DataTable.vue";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { useRoleTable } from "@/composables/roles/useRoles";
import { usePermission } from "@/composables/usePermission";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import type { BreadcrumbItem } from "@/types";
import type { Role, RolePageProps } from "@/types/role";
import { index as dashboardIndex } from "@/wayfinder/routes/dashboard";
import {
    create as rolesCreate,
    index as rolesIndex,
} from "@/wayfinder/routes/dashboard/roles";

const props = defineProps<RolePageProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: dashboardIndex().url },
    { title: "Roles", href: rolesIndex().url },
];

const {
    sorting,
    pagination,
    search,
    isLoading,
    columns,
    activeRole,
    deleteDialog,
} = useRoleTable(props.roles.meta, props.roles.filters);

const can = usePermission("role");
</script>

<template>
    <DashboardLayout :breadcrumbs="breadcrumbs">
        <Head title="Roles" />
        <div class="space-y-4 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Roles</h1>
                <Button
                    v-if="can('create')"
                    @click="router.visit(rolesCreate().url)"
                >
                    Add Role
                </Button>
            </div>

            <DataTable
                :isLoading="isLoading"
                :columns="columns"
                :data="roles"
                :sorting="sorting"
                :pagination="pagination"
                @update:sorting="(newSorting) => (sorting = newSorting)"
                @update:pagination="
                    (newPagination) => (pagination = newPagination)
                "
            >
                <template #filterSlot>
                    <div class="flex items-center gap-2">
                        <div class="relative max-w-sm">
                            <Input
                                v-model="search"
                                placeholder="Search..."
                                :class="search ? 'pr-8' : ''"
                            />
                            <button
                                v-if="search"
                                type="button"
                                class="absolute inset-y-0 right-2 flex items-center text-muted-foreground hover:text-foreground"
                                @click="search = ''"
                            >
                                <XIcon class="size-4" />
                            </button>
                        </div>
                    </div>
                </template>
            </DataTable>
        </div>

        <DeleteRoleDialog
            v-if="deleteDialog.isOpen.value"
            :open="deleteDialog.isOpen.value"
            :role="activeRole as Role"
            @update:open="deleteDialog.close()"
        />
    </DashboardLayout>
</template>
