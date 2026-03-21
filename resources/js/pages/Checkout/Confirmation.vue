<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import { Badge } from "@/components/ui/badge";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import HomeLayout from "@/layouts/HomeLayout.vue";
import type { Order } from "@/types/event";
import { index as ordersIndex } from "@/wayfinder/routes/orders";

defineProps<{
    order: Order;
}>();
</script>

<template>
    <HomeLayout>
        <Head title="Order Confirmed" />
        <div class="mx-auto max-w-2xl px-4 py-10">
            <div class="mb-8 text-center">
                <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-green-600">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-8 w-8"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M5 13l4 4L19 7"
                        />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold">Order Confirmed!</h1>
                <p class="mt-2 text-muted-foreground">
                    Your tickets for <strong>{{ order.event.title }}</strong> are confirmed.
                </p>
                <p class="mt-1 text-sm text-muted-foreground">
                    Order reference: <span class="font-mono">{{ order.uuid }}</span>
                </p>
            </div>

            <Card class="mb-6">
                <CardHeader>
                    <CardTitle>Your Tickets</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="divide-y">
                        <div
                            v-for="ticket in order.tickets"
                            :key="ticket.uuid"
                            class="flex items-center justify-between py-3"
                        >
                            <div>
                                <p class="font-medium text-sm">{{ ticket.ticket_type }}</p>
                                <p class="font-mono text-xs text-muted-foreground">
                                    {{ ticket.booking_reference }}
                                </p>
                            </div>
                            <Badge variant="secondary">{{ ticket.status }}</Badge>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <div class="text-center">
                <Link
                    :href="ordersIndex().url"
                    class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90 transition-colors"
                >
                    View My Orders
                </Link>
            </div>
        </div>
    </HomeLayout>
</template>
