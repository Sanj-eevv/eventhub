<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import { XIcon } from "lucide-vue-next";
import OrganizationConfirmDialog from "@/components/Dashboard/Organizations/OrganizationConfirmDialog.vue";
import OrganizationDeleteDialog from "@/components/Dashboard/Organizations/OrganizationDeleteDialog.vue";
import OrganizationFormDialog from "@/components/Dashboard/Organizations/OrganizationFormDialog.vue";
import DataTable from "@/components/DataTable.vue";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { useOrganizationTable } from "@/composables/organizations/useOrganizations";
import { usePermission } from "@/composables/usePermission";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import type { BreadcrumbItem } from "@/types";
import type { Organization, OrganizationStatus } from "@/types/organization";
import type { OrganizationPageProps } from "@/types/organization";
import { index as dashboardIndex } from "@/wayfinder/routes/dashboard";
import { index as orgsIndex } from "@/wayfinder/routes/dashboard/organizations";

const props = defineProps<OrganizationPageProps>();
const can = usePermission("organization");

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: dashboardIndex().url },
    { title: "Organizations", href: orgsIndex().url },
];

const {
    sorting,
    pagination,
    search,
    statusFilter,
    isLoading,
    columns,
    activeOrganization,
    createOrEditDialog,
    approveDialog,
    rejectDialog,
    deleteDialog,
} = useOrganizationTable(props.organizations.meta, props.organizations.filters);
const organizationStatus: Record<OrganizationStatus, string> = {
    pending: "Pending",
    suspended: "Suspended",
    rejected: "Rejected",
    approved: "Approved",
};
</script>

<template>
    <DashboardLayout :breadcrumbs="breadcrumbs">
        <Head title="Organization" />
        <div class="space-y-4 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Organizations</h1>
                <Button
                    v-if="can('create')"
                    @click="
                        activeOrganization = null;
                        createOrEditDialog.open();
                    "
                >
                    Add Organization
                </Button>
            </div>

            <DataTable
                :isLoading="isLoading"
                :columns="columns"
                :data="organizations"
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
                        <div class="relative">
                            <Select v-model="statusFilter">
                                <SelectTrigger class="w-40">
                                    <SelectValue placeholder="All statuses" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="(
                                            label, value
                                        ) in organizationStatus"
                                        :value="value"
                                        :key="value"
                                    >
                                        {{ label }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <button
                                v-if="statusFilter"
                                type="button"
                                class="absolute -right-5 inset-y-0 flex items-center text-muted-foreground hover:text-foreground"
                                @click="statusFilter = ''"
                            >
                                <XIcon class="size-4" />
                            </button>
                        </div>
                    </div>
                </template>
            </DataTable>
        </div>

        <OrganizationFormDialog
            v-if="createOrEditDialog.isOpen()"
            :open="createOrEditDialog.isOpen()"
            :organization="activeOrganization"
            @update:open="createOrEditDialog.close()"
        />

        <OrganizationConfirmDialog
            v-if="approveDialog.isOpen()"
            :open="approveDialog.isOpen()"
            action="approve"
            :organization="activeOrganization as Organization"
            @update:open="approveDialog.close()"
        />

        <OrganizationConfirmDialog
            v-if="rejectDialog.isOpen()"
            :open="rejectDialog.isOpen()"
            action="reject"
            :organization="activeOrganization as Organization"
            @update:open="rejectDialog.close()"
        />

        <OrganizationDeleteDialog
            v-if="deleteDialog.isOpen()"
            :open="deleteDialog.isOpen()"
            :organization="activeOrganization as Organization"
            @update:open="deleteDialog.close()"
        />
    </DashboardLayout>
</template>
