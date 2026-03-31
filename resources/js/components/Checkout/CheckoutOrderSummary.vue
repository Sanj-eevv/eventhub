<script setup lang="ts">
import { formatCurrency } from "@/lib/utils";
import type { OrderResource } from "@/types/order";

defineProps<{
    order: OrderResource
}>();
</script>

<template>
    <div
        class="bg-sf-surface border border-sf-border-subtle rounded-xl overflow-hidden mb-10 transition-colors duration-200"
    >
        <div class="px-5 py-4 border-b border-sf-border-subtle">
            <h2 class="font-display text-lg font-medium text-sf-text">
                Order Summary
            </h2>
        </div>
        <div class="px-5 py-4">
            <p class="font-body text-sm font-medium text-sf-text mb-4">
                {{ order.event.title }}
            </p>
            <div class="space-y-2">
                <div
                    v-for="ticket in order.tickets"
                    :key="ticket.uuid"
                    class="flex items-center justify-between text-sm"
                >
                    <span class="font-body text-sf-muted">
                        {{ ticket.ticket_type.name }}
                    </span>
                    <span class="font-code text-xs text-sf-tertiary">
                        {{ ticket.booking_reference }}
                    </span>
                </div>
            </div>
            <div
                class="flex items-center justify-between mt-5 pt-4 border-t border-sf-border-subtle"
            >
                <span class="font-body text-sm text-sf-muted">Total</span>
                <span class="font-display text-xl font-semibold text-sf-text">
                    {{ formatCurrency(order.total) }}
                </span>
            </div>
        </div>
    </div>
</template>
