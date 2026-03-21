<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
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

        <div class="mx-auto max-w-xl px-5 sm:px-8 py-20">

            <!-- Success mark -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center mb-8">
                    <div class="relative h-20 w-20">
                        <div class="absolute inset-0 rounded-full border border-sf-gold/30 animate-ping" />
                        <div class="absolute inset-0 rounded-full border border-sf-gold/20" />
                        <div class="relative h-full w-full rounded-full bg-sf-gold/10 border border-sf-gold/40 flex items-center justify-center">
                            <svg class="h-8 w-8 text-sf-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </div>
                    </div>
                </div>

                <p class="font-body text-xs tracking-[0.3em] uppercase text-sf-gold mb-4">Booking Confirmed</p>
                <h1 class="font-display font-semibold text-[clamp(2rem,5vw,3.5rem)] text-sf-text leading-tight mb-4">
                    You're going<br><em>to the show.</em>
                </h1>
                <p class="font-body font-light text-sf-muted mb-2">
                    Your tickets for <strong class="text-sf-text font-normal">{{ order.event.title }}</strong> are confirmed.
                </p>
                <p class="font-code text-xs text-sf-tertiary">Order ref: {{ order.uuid }}</p>
            </div>

            <!-- Tickets -->
            <div class="bg-sf-surface border border-sf-border-subtle rounded-xl overflow-hidden mb-8 transition-colors duration-200">
                <div class="px-5 py-4 border-b border-sf-border-subtle flex items-center gap-3">
                    <span class="h-px w-4 bg-sf-gold" />
                    <h2 class="font-display text-lg font-medium text-sf-text">Your Tickets</h2>
                </div>
                <div class="divide-y divide-sf-border-subtle">
                    <div v-for="ticket in order.tickets" :key="ticket.uuid" class="px-5 py-4 flex items-center justify-between">
                        <div>
                            <p class="font-body text-sm font-medium text-sf-text">{{ ticket.ticket_type }}</p>
                            <p class="font-code text-xs text-sf-tertiary mt-1">{{ ticket.booking_reference }}</p>
                        </div>
                        <span
                            :class="[
                                'font-body text-xs px-2.5 py-1 rounded border',
                                ticket.status === 'active'
                                    ? 'text-sf-gold border-sf-gold/30 bg-sf-gold/10'
                                    : 'text-sf-muted border-sf-border',
                            ]"
                        >
                            {{ ticket.status }}
                        </span>
                    </div>
                </div>
            </div>

            <p class="font-body text-sm text-sf-muted text-center mb-6">
                QR codes will be emailed to you and available in My Tickets.
            </p>

            <Link
                :href="ordersIndex()"
                class="flex items-center justify-center py-3.5 rounded bg-sf-ember text-white font-body text-sm tracking-wide hover:bg-sf-ember-hover active:scale-[0.99] transition-all duration-200"
            >
                View My Orders
            </Link>
        </div>
    </HomeLayout>
</template>
