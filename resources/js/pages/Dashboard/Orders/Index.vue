<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import { formatCurrency, formatDate } from "@/lib/utils";
import { Badge } from "@/components/ui/badge";
import { Card, CardContent } from "@/components/ui/card";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import type { BreadcrumbItem } from "@/types";
import type { Order } from "@/types/order";
import type { PaginatedResponse } from "@/types";
import { index as dashboardIndex } from "@/wayfinder/routes/dashboard";
import {
    index as dashboardOrdersIndex,
    show as dashboardOrdersShow,
} from "@/wayfinder/routes/dashboard/orders";

defineProps<{
    orders: PaginatedResponse<Order>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: dashboardIndex().url },
    { title: "Orders", href: dashboardOrdersIndex().url },
];

const statusVariantMap: Record<string, "default" | "secondary" | "destructive" | "outline"> = {
    paid: "default",
    reserved: "secondary",
    expired: "destructive",
    cancelled: "destructive",
};
</script>

<template>
    <DashboardLayout :breadcrumbs="breadcrumbs">
        <Head title="Orders" />
        <div class="space-y-4 p-6">
            <h1 class="text-2xl font-bold">Orders</h1>

            <div
                v-if="orders.data.length === 0"
                class="text-center text-muted-foreground py-16"
            >
                No orders found.
            </div>

            <div v-else class="space-y-3">
                <Card
                    v-for="order in orders.data"
                    :key="order.uuid"
                >
                    <CardContent class="flex items-center justify-between py-4">
                        <div class="space-y-0.5">
                            <p class="font-medium">{{ order.event.title }}</p>
                            <p class="font-mono text-xs text-muted-foreground">
                                {{ order.uuid }}
                            </p>
                            <p v-if="order.paid_at" class="text-xs text-muted-foreground">
                                Paid: {{ formatDate(order.paid_at) }}
                            </p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <p class="font-semibold">{{ formatCurrency(order.total) }}</p>
                                <Badge
                                    :variant="statusVariantMap[order.status.value] ?? 'outline'"
                                    class="mt-1"
                                >
                                    {{ order.status.label }}
                                </Badge>
                            </div>
                            <Link
                                :href="dashboardOrdersShow({ order: order.uuid }).url"
                                class="text-sm text-primary hover:underline"
                            >
                                View
                            </Link>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </DashboardLayout>
</template>
