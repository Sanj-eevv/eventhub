<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import { XIcon } from "lucide-vue-next";
import DeleteUserDialog from "@/components/Dashboard/Users/DeleteUserDialog.vue";
import UserFormDialog from "@/components/Dashboard/Users/UserFormDialog.vue";
import DataTable from "@/components/DataTable.vue";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { usePermission } from "@/composables/usePermission";
import { useUserTable } from "@/composables/users/useUsers";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import type { BreadcrumbItem, FilteredResponse } from "@/types";
import type { OrganizationPicker } from "@/types/organization";
import type { RolePicker } from "@/types/role";
import type { User, UserFilterProps } from "@/types/user";
import { index as dashboardIndex } from "@/wayfinder/routes/dashboard";
import { index as usersIndex } from "@/wayfinder/routes/dashboard/users";

type PageProps = {
    users: FilteredResponse<User, UserFilterProps>;
    roles: RolePicker[];
    organizations: OrganizationPicker[];
};

const props = defineProps<PageProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: dashboardIndex().url },
    { title: "Users", href: usersIndex().url },
];
const can = usePermission("user");

const {
    sorting,
    pagination,
    search,
    isLoading,
    columns,
    activeUser,
    createOrEditDialog,
    deleteDialog,
} = useUserTable(props.users.meta, props.users.filters);
</script>

<template>
    <DashboardLayout :breadcrumbs="breadcrumbs">
        <Head title="User" />
        <div class="space-y-4 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Users</h1>
                <Button
                    v-if="can('create')"
                    @click="
                        activeUser = null;
                        createOrEditDialog.open();
                    "
                >
                    Add User
                </Button>
            </div>

            <DataTable
                :isLoading="isLoading"
                :columns="columns"
                :data="users"
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

        <UserFormDialog
            v-if="createOrEditDialog.isOpen.value"
            :open="createOrEditDialog.isOpen.value"
            :user="activeUser ?? undefined"
            :roles="roles"
            :organizations="organizations"
            @update:open="createOrEditDialog.close()"
        />

        <DeleteUserDialog
            v-if="deleteDialog.isOpen.value"
            :open="deleteDialog.isOpen.value"
            :user="activeUser as User"
            @update:open="deleteDialog.close()"
        />
    </DashboardLayout>
</template>
