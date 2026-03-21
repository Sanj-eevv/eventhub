<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import { Badge } from "@/components/ui/badge";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import HomeLayout from "@/layouts/HomeLayout.vue";
import type { Order } from "@/types/event";
import { index as ordersIndex } from "@/wayfinder/routes/orders";
import { show as ticketShow } from "@/wayfinder/routes/tickets";

const props = defineProps<{
    order: Order;
}>();

const ticketStatusVariantMap: Record<string, "default" | "secondary" | "destructive" | "outline"> = {
    active: "default",
    pending: "outline",
    used: "secondary",
    cancelled: "destructive",
};
</script>

<template>
    <HomeLayout>
        <Head :title="`Order #${order.uuid}`" />
        <div class="mx-auto max-w-2xl px-4 py-10">
            <div class="mb-6 flex items-center gap-4">
                <Link
                    :href="ordersIndex().url"
                    class="text-sm text-muted-foreground hover:text-foreground"
                >
                    ← My Orders
                </Link>
            </div>

            <h1 class="mb-2 text-2xl font-bold">{{ order.event.title }}</h1>
            <p class="mb-6 font-mono text-sm text-muted-foreground">
                Order #{{ order.uuid }}
            </p>

            <Card class="mb-6">
                <CardHeader>
                    <CardTitle>Order Details</CardTitle>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-muted-foreground">Status</span>
                        <Badge>{{ order.status.label }}</Badge>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-muted-foreground">Total</span>
                        <span class="font-semibold">{{ order.total_formatted }}</span>
                    </div>
                    <div v-if="order.paid_at" class="flex justify-between text-sm">
                        <span class="text-muted-foreground">Paid at</span>
                        <span>{{ order.paid_at }}</span>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Tickets</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="divide-y">
                        <div
                            v-for="ticket in order.tickets"
                            :key="ticket.uuid"
                            class="py-4 space-y-3"
                        >
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="font-medium text-sm">{{ ticket.ticket_type }}</p>
                                    <p class="font-mono text-xs text-muted-foreground mt-0.5">
                                        {{ ticket.booking_reference }}
                                    </p>
                                    <p
                                        v-if="ticket.attendee_name"
                                        class="text-xs text-muted-foreground mt-0.5"
                                    >
                                        {{ ticket.attendee_name }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Badge
                                        :variant="ticketStatusVariantMap[ticket.status] ?? 'outline'"
                                    >
                                        {{ ticket.status }}
                                    </Badge>
                                    <Link
                                        :href="ticketShow({ ticket: ticket.uuid }).url"
                                        class="text-xs text-primary hover:underline"
                                    >
                                        View
                                    </Link>
                                </div>
                            </div>
                            <img
                                v-if="ticket.qr_code_path"
                                :src="ticket.qr_code_path"
                                :alt="`QR code for ${ticket.booking_reference}`"
                                class="h-32 w-32 rounded-md"
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </HomeLayout>
</template>
