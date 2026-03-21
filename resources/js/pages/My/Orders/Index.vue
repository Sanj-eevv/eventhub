<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import { Badge } from "@/components/ui/badge";
import { Card, CardContent } from "@/components/ui/card";
import HomeLayout from "@/layouts/HomeLayout.vue";
import type { PaginatedResponse } from "@/types";
import type { Order } from "@/types/event";
import { show as ordersShow } from "@/wayfinder/routes/orders";

defineProps<{
    orders: PaginatedResponse<Order>;
}>();

const statusVariantMap: Record<string, "default" | "secondary" | "destructive" | "outline"> = {
    paid: "default",
    reserved: "secondary",
    pending: "outline",
    expired: "destructive",
    cancelled: "destructive",
};
</script>

<template>
    <HomeLayout>
        <Head title="My Orders" />
        <div class="mx-auto max-w-3xl px-4 py-10">
            <h1 class="mb-6 text-2xl font-bold">My Orders</h1>

            <div
                v-if="orders.data.length === 0"
                class="text-center text-muted-foreground py-16"
            >
                You have no orders yet.
            </div>

            <div v-else class="space-y-4">
                <Card
                    v-for="order in orders.data"
                    :key="order.uuid"
                >
                    <CardContent class="flex items-center justify-between py-4">
                        <div class="space-y-1">
                            <p class="font-medium">{{ order.event.title }}</p>
                            <p class="text-sm text-muted-foreground font-mono">
                                {{ order.uuid }}
                            </p>
                            <p v-if="order.paid_at" class="text-xs text-muted-foreground">
                                Paid: {{ order.paid_at }}
                            </p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <p class="font-semibold">{{ order.total_formatted }}</p>
                                <Badge
                                    :variant="statusVariantMap[order.status.value] ?? 'outline'"
                                    class="mt-1"
                                >
                                    {{ order.status.label }}
                                </Badge>
                            </div>
                            <Link
                                :href="ordersShow({ order: order.uuid }).url"
                                class="text-sm text-primary hover:underline"
                            >
                                View
                            </Link>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </HomeLayout>
</template>
