<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import HomeLayout from "@/layouts/HomeLayout.vue";
import { formatDate, formatTime } from "@/lib/utils";
import type { Order } from "@/types/order";
import { index as ordersIndex } from "@/wayfinder/routes/orders";
import { show as ticketShow } from "@/wayfinder/routes/tickets";

const props = defineProps<{
    order: Order;
}>();

const ticketStatusConfig: Record<string, { classes: string }> = {
    active: { classes: "text-sf-gold border-sf-gold/30 bg-sf-gold/10" },
    pending: { classes: "text-sf-muted border-sf-border bg-transparent" },
    used: { classes: "text-sf-muted border-sf-border bg-sf-surface-raised" },
    cancelled: { classes: "text-sf-ember border-sf-ember/30 bg-sf-ember/10" },
};

</script>

<template>
    <HomeLayout>
        <Head :title="`Order — ${order.event.title}`" />

        <div class="mx-auto max-w-2xl px-5 sm:px-8 py-16">

            <!-- Back -->
            <Link
                :href="ordersIndex()"
                class="inline-flex items-center gap-2 font-body text-sm text-sf-tertiary hover:text-sf-muted transition-colors mb-10"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                My Orders
            </Link>

            <!-- Header -->
            <div class="mb-10">
                <p class="font-code text-xs text-sf-tertiary mb-3 tracking-wider">{{ order.uuid }}</p>
                <h1 class="font-display font-semibold text-[clamp(1.75rem,4vw,3rem)] text-sf-text leading-tight">
                    {{ order.event.title }}
                </h1>
            </div>

            <!-- Order details -->
            <div class="bg-sf-surface border border-sf-border-subtle rounded-xl overflow-hidden mb-6 transition-colors duration-200">
                <div class="px-5 py-4 border-b border-sf-border-subtle flex items-center gap-3">
                    <span class="h-px w-4 bg-sf-gold" />
                    <h2 class="font-display text-lg font-medium text-sf-text">Order Details</h2>
                </div>
                <div class="px-5 py-4 space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="font-body text-sf-muted">Status</span>
                        <span
                            :class="[
                                'font-body text-xs px-2.5 py-1 rounded border',
                                order.status.value === 'paid' ? 'text-sf-gold border-sf-gold/30 bg-sf-gold/10' :
                                order.status.value === 'expired' || order.status.value === 'cancelled' ? 'text-sf-ember border-sf-ember/30 bg-sf-ember/10' :
                                'text-sf-muted border-sf-border',
                            ]"
                        >
                            {{ order.status.label }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="font-body text-sf-muted">Total</span>
                        <span class="font-display text-xl font-medium text-sf-text">{{ order.total_formatted }}</span>
                    </div>
                    <div v-if="order.paid_at" class="flex justify-between items-center text-sm">
                        <span class="font-body text-sf-muted">Paid at</span>
                        <span class="font-body text-sm text-sf-text">{{ formatDate(order.paid_at) }}</span>
                    </div>
                </div>
            </div>

            <!-- Tickets -->
            <div class="bg-sf-surface border border-sf-border-subtle rounded-xl overflow-hidden transition-colors duration-200">
                <div class="px-5 py-4 border-b border-sf-border-subtle flex items-center gap-3">
                    <span class="h-px w-4 bg-sf-gold" />
                    <h2 class="font-display text-lg font-medium text-sf-text">Tickets</h2>
                </div>
                <div class="divide-y divide-sf-border-subtle">
                    <div
                        v-for="ticket in order.tickets"
                        :key="ticket.uuid"
                        class="px-5 py-5 space-y-4"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-body text-sm font-medium text-sf-text">{{ ticket.ticket_type }}</p>
                                <p class="font-code text-xs text-sf-tertiary mt-1">{{ ticket.booking_reference }}</p>
                                <p v-if="ticket.attendee_name" class="font-body text-xs text-sf-muted mt-1">
                                    {{ ticket.attendee_name }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <span
                                    :class="[
                                        'font-body text-xs px-2.5 py-1 rounded border',
                                        ticketStatusConfig[ticket.status]?.classes ?? 'text-sf-muted border-sf-border',
                                    ]"
                                >
                                    {{ ticket.status }}
                                </span>
                                <Link
                                    :href="ticketShow({ ticket: ticket.uuid })"
                                    class="font-body text-xs text-sf-gold hover:text-sf-text transition-colors"
                                >
                                    View ticket →
                                </Link>
                            </div>
                        </div>
                        <img
                            v-if="ticket.qr_code_path"
                            :src="ticket.qr_code_path"
                            :alt="`QR code for ${ticket.booking_reference}`"
                            class="h-28 w-28 rounded-lg border border-sf-border"
                        />
                    </div>
                </div>
            </div>
        </div>
    </HomeLayout>
</template>
