<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import { computed, reactive } from "vue";
import { formatCurrency } from "@/lib/utils";
import type { TicketTypeResource } from "@/types/event";
import { reserve } from "@/wayfinder/routes/tickets";

const props = defineProps<{
    ticketTypes: TicketTypeResource[];
    eventSlug: string;
}>();

const quantities = reactive<Record<string, number>>(
    Object.fromEntries(
        props.ticketTypes.map((ticketType) => [ticketType.uuid, 0]),
    ),
);

const form = useForm(() => ({
    items: props.ticketTypes
        .filter((ticketType) => quantities[ticketType.uuid] > 0)
        .map((ticketType) => ({
            ticket_type_uuid: ticketType.uuid,
            quantity: quantities[ticketType.uuid],
        })),
}));

const hasSelection = computed(() =>
    props.ticketTypes.some((ticketType) => quantities[ticketType.uuid] > 0),
);

const orderTotal = computed(() =>
    props.ticketTypes.reduce((sum, ticketType) => {
        return sum + ticketType.price * (quantities[ticketType.uuid] ?? 0);
    }, 0),
);

const formatTotal = computed(() =>
    orderTotal.value > 0 ? formatCurrency(orderTotal.value) : null,
);

function increment(ticketType: TicketTypeResource): void {
    const max = ticketType.max_per_user;
    quantities[ticketType.uuid] =
        max != null
            ? Math.min(max, quantities[ticketType.uuid] + 1)
            : quantities[ticketType.uuid] + 1;
}

function decrement(ticketType: TicketTypeResource): void {
    quantities[ticketType.uuid] = Math.max(0, quantities[ticketType.uuid] - 1);
}

function isAtMax(ticketType: TicketTypeResource): boolean {
    const max = ticketType.max_per_user;
    return max != null && quantities[ticketType.uuid] >= max;
}

function submit(): void {
    form.post(reserve({ event: props.eventSlug }).url);
}
</script>

<template>
    <div class="sticky top-24 space-y-4">
        <div
            v-for="ticketType in ticketTypes"
            :key="ticketType.uuid"
            :class="[
                'bg-sf-surface border rounded-xl p-5 transition-all duration-200',
                quantities[ticketType.uuid] > 0
                    ? 'border-sf-gold/40 bg-sf-gold/5'
                    : 'border-sf-border-subtle hover:border-sf-border',
            ]"
        >
            <div class="flex items-start justify-between gap-4 mb-4">
                <div class="flex-1">
                    <p class="font-display text-base font-medium text-sf-text">
                        {{ ticketType.name }}
                    </p>
                    <p
                        v-if="ticketType.description"
                        class="font-body text-sm text-sf-muted mt-1 leading-relaxed"
                    >
                        {{ ticketType.description }}
                    </p>
                </div>
                <span
                    class="font-display text-xl font-medium text-sf-text shrink-0"
                    >{{ formatCurrency(ticketType.price) }}</span
                >
            </div>

            <div class="flex items-center gap-3">
                <button
                    type="button"
                    class="h-8 w-8 flex items-center justify-center rounded border border-sf-border text-sf-muted hover:border-sf-gold hover:text-sf-gold transition-all disabled:opacity-30 disabled:pointer-events-none text-lg leading-none"
                    :disabled="quantities[ticketType.uuid] === 0"
                    @click="decrement(ticketType)"
                >
                    −
                </button>
                <span
                    class="font-code text-sm text-sf-text w-6 text-center tabular-nums"
                    >{{ quantities[ticketType.uuid] }}</span
                >
                <button
                    type="button"
                    class="h-8 w-8 flex items-center justify-center rounded border border-sf-border text-sf-muted hover:border-sf-gold hover:text-sf-gold transition-all disabled:opacity-30 disabled:pointer-events-none text-lg leading-none"
                    :disabled="isAtMax(ticketType)"
                    @click="increment(ticketType)"
                >
                    +
                </button>
                <span
                    v-if="ticketType.max_per_user != null"
                    class="font-body text-xs text-sf-tertiary ml-auto"
                    >max {{ ticketType.max_per_user }}</span
                >
            </div>
        </div>

        <div
            v-if="ticketTypes.length === 0"
            class="bg-sf-surface border border-sf-border-subtle rounded-xl p-6 text-center"
        >
            <p class="font-body text-sm text-sf-tertiary">
                No tickets available yet.
            </p>
        </div>

        <div v-if="ticketTypes.length > 0" class="pt-1 space-y-3">
            <div
                v-if="formatTotal"
                class="flex justify-between items-center px-1"
            >
                <span class="font-body text-sm text-sf-muted">Total</span>
                <span class="font-display text-xl font-medium text-sf-text">{{
                    formatTotal
                }}</span>
            </div>
            <button
                type="button"
                :disabled="!hasSelection || form.processing"
                class="w-full py-3.5 rounded bg-sf-ember text-white font-body text-sm tracking-wide hover:bg-sf-ember-hover active:scale-95 transition-all duration-200 disabled:opacity-40 disabled:pointer-events-none"
                @click="submit"
            >
                <span v-if="form.processing">Reserving…</span>
                <span v-else-if="hasSelection">Reserve Tickets</span>
                <span v-else>Select tickets above</span>
            </button>
            <p
                v-if="form.errors.items"
                class="font-body text-xs text-sf-ember text-center"
            >
                {{ form.errors.items }}
            </p>
            <p class="font-body text-xs text-sf-tertiary text-center">
                Secure Stripe checkout
            </p>
        </div>
    </div>
</template>
