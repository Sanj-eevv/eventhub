<script setup lang="ts">
import { useForm, usePoll } from "@inertiajs/vue3";
import { computed } from "vue";
import { useTicketSaleState } from "@/composables/events/useTicketSaleState";
import { useTicketSelection } from "@/composables/events/useTicketSelection";
import { formatCurrency, formatDate, formatTime } from "@/lib/utils";
import type { TicketTypeResource } from "@/types/event";
import { reserve } from "@/wayfinder/routes/tickets";

const props = defineProps<{
    ticketTypes: TicketTypeResource[];
    eventSlug: string;
    eventTimezone: string;
}>();

usePoll(10_000, { only: ["ticketTypes"] });

const {
    quantities,
    hasSelection,
    orderTotal,
    selectedItems,
    increment,
    decrement,
    isAtMax,
} = useTicketSelection(props.ticketTypes);

const { states, countdowns } = useTicketSaleState(() => props.ticketTypes);

const form = useForm({
    items: [] as Array<{ ticket_type_uuid: string; quantity: number }>,
});

const formattedTotal = computed(() =>
    orderTotal.value > 0 ? formatCurrency(orderTotal.value) : null,
);

function submit(): void {
    form.transform(() => ({
        items: selectedItems.value.filter(
            (item) => states.value[item.ticket_type_uuid] === "on_sale",
        ),
    })).post(reserve({ event: props.eventSlug }).url);
}
</script>

<template>
    <div class="sticky top-24 space-y-4">
        <div
            v-for="ticketType in ticketTypes"
            :key="ticketType.uuid"
            :class="[
                'bg-sf-surface border rounded-xl p-5 transition-all duration-200',
                states[ticketType.uuid] === 'on_sale'
                    ? quantities[ticketType.uuid] > 0
                        ? 'border-sf-gold/40 bg-sf-gold/5'
                        : 'border-sf-border-subtle hover:border-sf-border'
                    : 'border-sf-border-subtle opacity-70',
            ]"
        >
            <div class="flex items-start justify-between gap-4 mb-4">
                <div class="flex-1">
                    <p class="font-display text-base font-medium text-sf-text">
                        {{ ticketType.name }}
                    </p>
                    <p
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
                <template v-if="states[ticketType.uuid] === 'on_sale'">
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
                    <div class="ml-auto flex flex-col items-end gap-0.5">
                        <span
                            v-if="ticketType.max_per_user != null"
                            class="font-body text-xs text-sf-tertiary"
                            >max {{ ticketType.max_per_user }}</span
                        >
                        <span
                            :class="[
                                'font-body text-xs tabular-nums',
                                ticketType.available_capacity <= 10
                                    ? 'text-sf-gold font-medium'
                                    : 'text-sf-tertiary',
                            ]"
                        >
                            {{
                                ticketType.available_capacity <= 10
                                    ? `Only ${ticketType.available_capacity} left!`
                                    : `${ticketType.available_capacity} available`
                            }}
                        </span>
                    </div>
                </template>

                <template v-else-if="states[ticketType.uuid] === 'upcoming'">
                    <span class="font-body text-xs text-sf-muted"
                        >Opens in</span
                    >
                    <span class="font-code text-sm text-sf-gold tabular-nums">{{
                        countdowns[ticketType.uuid]
                    }}</span>
                </template>

                <span
                    v-else-if="states[ticketType.uuid] === 'ended'"
                    class="font-body text-xs text-sf-tertiary"
                    >Sale ended</span
                >

                <span
                    v-else-if="states[ticketType.uuid] === 'sold_out'"
                    class="font-body text-xs text-sf-tertiary"
                    >Sold out</span
                >
            </div>

            <p
                v-if="
                    states[ticketType.uuid] === 'on_sale' &&
                    ticketType.sale_ends_at
                "
                class="font-body text-xs text-sf-tertiary mt-2"
            >
                Sale ends
                {{ formatDate(ticketType.sale_ends_at, props.eventTimezone) }} ·
                {{ formatTime(ticketType.sale_ends_at, props.eventTimezone) }}
            </p>
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
                v-if="formattedTotal"
                class="flex justify-between items-center px-1"
            >
                <span class="font-body text-sm text-sf-muted">Total</span>
                <span class="font-display text-xl font-medium text-sf-text">{{
                    formattedTotal
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
        </div>
    </div>
</template>
