<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import { Badge } from "@/components/ui/badge";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import HomeLayout from "@/layouts/HomeLayout.vue";
import type { OrderTicket } from "@/types/event";
import { index as ordersIndex } from "@/wayfinder/routes/orders";

type TicketDetail = OrderTicket & {
    event: { title: string; slug: string };
    ticket_type: string;
};

defineProps<{
    ticket: TicketDetail;
}>();
</script>

<template>
    <HomeLayout>
        <Head :title="ticket.booking_reference" />
        <div class="mx-auto max-w-sm px-4 py-10">
            <div class="mb-6">
                <Link
                    :href="ordersIndex().url"
                    class="text-sm text-muted-foreground hover:text-foreground"
                >
                    ← My Orders
                </Link>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle class="text-center text-xl">
                        {{ ticket.event.title }}
                    </CardTitle>
                </CardHeader>
                <CardContent class="flex flex-col items-center space-y-6">
                    <div
                        v-if="ticket.qr_code_path"
                        class="flex justify-center"
                    >
                        <img
                            :src="ticket.qr_code_path"
                            :alt="`QR code for ${ticket.booking_reference}`"
                            class="h-56 w-56 rounded-lg"
                        />
                    </div>
                    <div
                        v-else
                        class="flex h-56 w-56 items-center justify-center rounded-lg bg-muted text-muted-foreground text-sm"
                    >
                        QR code unavailable
                    </div>

                    <div class="w-full space-y-3 text-center">
                        <p class="font-mono text-2xl font-bold tracking-widest">
                            {{ ticket.booking_reference }}
                        </p>
                        <p class="text-sm text-muted-foreground">{{ ticket.ticket_type }}</p>
                        <p
                            v-if="ticket.attendee_name"
                            class="text-sm"
                        >
                            {{ ticket.attendee_name }}
                        </p>
                        <Badge class="text-xs">{{ ticket.status }}</Badge>
                    </div>
                </CardContent>
            </Card>
        </div>
    </HomeLayout>
</template>
