<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import PageContainer from "@/components/PageContainer.vue";
import HomeLayout from "@/layouts/HomeLayout.vue";
import { formatCurrency, formatDate } from "@/lib/utils";
import type { PaginatedResponse } from "@/types";
import type { Order } from "@/types/order";
import { show as ordersShow } from "@/wayfinder/routes/orders";

defineProps<{
    orders: PaginatedResponse<Order>;
}>();

const statusDotClass: Record<string, string> = {
    paid: "bg-sf-gold",
    reserved: "bg-[#7ab8d4]",
    expired: "bg-sf-ember",
    cancelled: "bg-sf-ember",
};

const statusBadgeClass: Record<string, string> = {
    paid: "text-sf-gold border-sf-gold/30 bg-sf-gold/10",
    reserved: "text-[#7ab8d4] border-[#7ab8d4]/30 bg-[#7ab8d4]/10",
    expired: "text-sf-ember border-sf-ember/30 bg-sf-ember/10",
    cancelled: "text-sf-ember border-sf-ember/30 bg-sf-ember/10",
};

</script>

<template>
    <HomeLayout>
        <Head title="My Orders" />

        <PageContainer>

            <div class="mb-12">
                <p class="font-body text-xs tracking-[0.3em] uppercase text-sf-gold mb-3">Account</p>
                <h1 class="font-display font-semibold text-[clamp(2rem,5vw,3.5rem)] text-sf-text leading-none">
                    My Orders
                </h1>
            </div>

            <!-- Empty -->
            <div v-if="orders.data.length === 0" class="py-24 text-center">
                <div class="inline-flex h-14 w-14 items-center justify-center rounded-full border border-sf-border mb-5">
                    <svg class="h-6 w-6 text-sf-tertiary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                    </svg>
                </div>
                <p class="font-display text-lg font-medium text-sf-tertiary">No orders yet</p>
                <p class="font-body text-sm text-sf-tertiary mt-1 mb-6">Your ticket purchases will appear here.</p>
                <Link href="/events" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sf-ember text-white font-body text-sm rounded hover:bg-sf-ember-hover transition-colors">
                    Browse Events
                </Link>
            </div>

            <!-- Orders list -->
            <div v-else class="space-y-3">
                <Link
                    v-for="order in orders.data"
                    :key="order.uuid"
                    :href="ordersShow({ order: order.uuid })"
                    class="group flex items-center gap-5 bg-sf-surface border border-sf-border-subtle rounded-xl px-5 py-5 hover:border-sf-border hover:bg-sf-surface-raised transition-all duration-200"
                >
                    <!-- Status dot -->
                    <div :class="['h-2 w-2 rounded-full shrink-0 mt-0.5', statusDotClass[order.status.value] ?? 'bg-sf-tertiary']" />

                    <!-- Details -->
                    <div class="flex-1 min-w-0">
                        <p class="font-display text-base font-medium text-sf-text truncate group-hover:text-sf-text transition-colors">
                            {{ order.event.title }}
                        </p>
                        <div class="flex items-center gap-3 mt-1">
                            <span v-if="order.paid_at" class="font-body text-xs text-sf-tertiary shrink-0">Paid {{ formatDate(order.paid_at) }}</span>
                        </div>
                    </div>

                    <!-- Right side -->
                    <div class="flex items-center gap-4 shrink-0">
                        <div class="text-right">
                            <p class="font-display text-lg font-medium text-sf-text">{{ formatCurrency(order.total) }}</p>
                            <span :class="['inline-block font-body text-xs px-2 py-0.5 rounded border mt-1', statusBadgeClass[order.status.value] ?? 'text-sf-muted border-sf-border']">
                                {{ order.status.label }}
                            </span>
                        </div>
                        <svg class="h-4 w-4 text-sf-tertiary group-hover:text-sf-muted group-hover:translate-x-0.5 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </div>
                </Link>
            </div>
        </PageContainer>
    </HomeLayout>
</template>
