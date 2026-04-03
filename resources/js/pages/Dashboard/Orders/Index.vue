<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import { XIcon } from "lucide-vue-next";
import DataTable from "@/components/DataTable.vue";
import { Input } from "@/components/ui/input";
import { useOrderTable } from "@/composables/orders/useOrders";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import type { BreadcrumbItem, FilteredResponse } from "@/types";
import type { OrderFilterProps, OrderIndexItem } from "@/types/order";
import { index as dashboardIndex } from "@/wayfinder/routes/dashboard";
import { index as ordersIndex } from "@/wayfinder/routes/dashboard/orders";

type PageProps = {
    orders: FilteredResponse<OrderIndexItem, OrderFilterProps>;
};

const props = defineProps<PageProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: dashboardIndex().url },
    { title: "Orders", href: ordersIndex().url },
];

const { isLoading, sorting, pagination, columns, search } = useOrderTable(
    props.orders.meta,
    props.orders.filters,
);
</script>

<template>
    <DashboardLayout :breadcrumbs="breadcrumbs">
        <Head title="Orders" />
        <div class="space-y-4 p-6">
            <h1 class="text-2xl font-bold">Orders</h1>

            <DataTable
                :isLoading="isLoading"
                :columns="columns"
                :data="orders"
                :sorting="sorting"
                :pagination="pagination"
                @update:sorting="(newSorting) => (sorting = newSorting)"
                @update:pagination="(newPagination) => (pagination = newPagination)"
            >
                <template #filterSlot>
                    <div class="relative max-w-sm">
                        <Input
                            v-model="search"
                            placeholder="Search by customer or event..."
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
                </template>
            </DataTable>
        </div>
    </DashboardLayout>
</template>
