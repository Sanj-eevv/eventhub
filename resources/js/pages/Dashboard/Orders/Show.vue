<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import { formatCurrency, formatDate } from "@/lib/utils";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import type { BreadcrumbItem } from "@/types";
import type { OrderResource } from "@/types/order";
import { index as dashboardIndex } from "@/wayfinder/routes/dashboard";
import {
    index as dashboardOrdersIndex,
} from "@/wayfinder/routes/dashboard/orders";

const props = defineProps<{
    order: OrderResource
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: dashboardIndex().url },
    { title: "Orders", href: dashboardOrdersIndex().url },
    { title: `Order #${props.order.uuid}` },
];

const ticketStatusVariantMap: Record<string, "default" | "secondary" | "destructive" | "outline"> = {
    active: "default",
    pending: "outline",
    used: "secondary",
    cancelled: "destructive",
};

const goBack = () => window.history.back();
</script>

<template>
    <DashboardLayout :breadcrumbs="breadcrumbs">
        <Head :title="`Order #${order.uuid}`" />
        <div class="space-y-6 p-6">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="sm" @click="goBack">
                    Back
                </Button>
                <h1 class="text-2xl font-bold">Order Details</h1>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Summary</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-muted-foreground">Event</span>
                            <span class="font-medium">{{ order.event.title }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-muted-foreground">Order ID</span>
                            <span class="font-mono">{{ order.uuid }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-muted-foreground">Status</span>
                            <Badge>{{ order.status.label }}</Badge>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-muted-foreground">Total</span>
                            <span class="font-semibold">{{ formatCurrency(order.total) }}</span>
                        </div>
                        <div v-if="order.paid_at" class="flex justify-between text-sm">
                            <span class="text-muted-foreground">Paid at</span>
                            <span>{{ formatDate(order.paid_at) }}</span>
                        </div>
                        <div v-if="order.reserved_at" class="flex justify-between text-sm">
                            <span class="text-muted-foreground">Reserved at</span>
                            <span>{{ formatDate(order.reserved_at) }}</span>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Tickets ({{ order.tickets.length }})</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="divide-y">
                            <div
                                v-for="ticket in order.tickets"
                                :key="ticket.uuid"
                                class="py-3 space-y-1"
                            >
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="font-medium text-sm">{{ ticket.ticket_type.name }}</p>
                                        <p class="font-mono text-xs text-muted-foreground">
                                            {{ ticket.booking_reference }}
                                        </p>
                                        <p
                                            v-if="ticket.attendee_name"
                                            class="text-xs text-muted-foreground mt-0.5"
                                        >
                                            {{ ticket.attendee_name }}
                                        </p>
                                    </div>
                                    <Badge
                                        :variant="ticketStatusVariantMap[ticket.status] ?? 'outline'"
                                    >
                                        {{ ticket.status }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </DashboardLayout>
</template>
