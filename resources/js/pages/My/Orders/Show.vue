<script setup lang="ts">
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import PageContainer from "@/components/PageContainer.vue";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from "@/components/ui/alert-dialog";
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from "@/components/ui/tooltip";
import HomeLayout from "@/layouts/HomeLayout.vue";
import { formatCurrency, formatDateTime } from "@/lib/utils";
import type { OrderResource } from "@/types/order";
import { show as checkoutShow } from "@/wayfinder/routes/checkout";
import { show as eventShow } from "@/wayfinder/routes/events";
import {
    cancel as orderCancel,
    index as ordersIndex,
} from "@/wayfinder/routes/orders";

const props = defineProps<{
    order: OrderResource;
}>();

const isActiveReservation = computed(
    () =>
        props.order.status.value === "reserved" &&
        props.order.expires_at !== null &&
        new Date(props.order.expires_at) > new Date(),
);

const cancelForm = useForm({});
const showCancelDialog = ref(false);

function cancelOrder(): void {
    cancelForm.delete(orderCancel({ order: props.order.uuid }).url);
}

const ticketStatusConfig: Record<string, { classes: string }> = {
    active: { classes: "text-sf-gold border-sf-gold/30 bg-sf-gold/10" },
    pending: { classes: "text-sf-muted border-sf-border bg-transparent" },
    used: { classes: "text-sf-muted border-sf-border bg-sf-surface-raised" },
    cancelled: { classes: "text-sf-ember border-sf-ember/30 bg-sf-ember/10" },
};
</script>

<template>
    <HomeLayout>
        <Head :title="`Order — ${order.event!.title}`" />

        <PageContainer>
            <Link
                :href="ordersIndex()"
                class="inline-flex items-center gap-2 font-body text-sm text-sf-muted hover:text-sf-text transition-colors mb-10"
            >
                <svg
                    class="h-4 w-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"
                    />
                </svg>
                My Orders
            </Link>
            <div class="mb-10">
                <Link
                    :href="eventShow({ event: order.event!.slug }).url"
                    class="font-display font-semibold text-[clamp(1.75rem,4vw,3rem)] text-sf-text leading-tight hover:text-sf-muted transition-colors"
                >
                    {{ order.event!.title }}
                </Link>
            </div>
            <div
                class="bg-sf-surface border border-sf-border-subtle rounded-xl overflow-hidden mb-6 transition-colors duration-200"
            >
                <div
                    class="px-5 py-4 border-b border-sf-border-subtle flex items-center gap-3"
                >
                    <span class="h-px w-4 bg-sf-gold" />
                    <h2 class="font-display text-lg font-medium text-sf-text">
                        Order Details
                    </h2>
                </div>
                <div class="px-5 py-4 space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="font-body text-sf-muted">Status</span>
                        <span
                            :class="[
                                'font-body text-xs px-2.5 py-1 rounded border',
                                order.status.value === 'paid'
                                    ? 'text-sf-gold border-sf-gold/30 bg-sf-gold/10'
                                    : order.status.value === 'cancelled'
                                      ? 'text-sf-ember border-sf-ember/30 bg-sf-ember/10'
                                      : 'text-sf-muted border-sf-border',
                            ]"
                        >
                            {{ order.status.label }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="font-body text-sf-muted">Total</span>
                        <span
                            class="font-display text-xl font-medium text-sf-text"
                            >{{ formatCurrency(order.total) }}</span
                        >
                    </div>
                    <div
                        v-if="order.paid_at"
                        class="flex justify-between items-center text-sm"
                    >
                        <span class="font-body text-sf-muted">Paid at</span>
                        <span class="font-body text-sm text-sf-text">{{
                            formatDateTime(order.paid_at)
                        }}</span>
                    </div>
                </div>
                <div
                    v-if="order.status.value === 'paid'"
                    class="px-5 py-4 border-t border-sf-border-subtle"
                >
                    <TooltipProvider>
                        <Tooltip>
                            <TooltipTrigger as-child>
                                <button
                                    :disabled="
                                        !order.can_cancel ||
                                        cancelForm.processing
                                    "
                                    class="font-body text-xs text-sf-ember hover:text-sf-text transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    @click="
                                        order.can_cancel &&
                                        (showCancelDialog = true)
                                    "
                                >
                                    Cancel order
                                </button>
                            </TooltipTrigger>
                            <TooltipContent v-if="!order.can_cancel">
                                <p class="font-body text-xs">
                                    The cancellation window for this order has
                                    passed.
                                </p>
                            </TooltipContent>
                        </Tooltip>
                    </TooltipProvider>
                </div>
            </div>
            <div
                v-if="order.refund_status"
                class="bg-sf-surface border border-sf-border-subtle rounded-xl overflow-hidden mb-6 transition-colors duration-200"
            >
                <div
                    class="px-5 py-4 border-b border-sf-border-subtle flex items-center gap-3"
                >
                    <span class="h-px w-4 bg-sf-gold" />
                    <h2 class="font-display text-lg font-medium text-sf-text">
                        Refund
                    </h2>
                </div>
                <div class="px-5 py-4 space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="font-body text-sf-muted">Status</span>
                        <span
                            class="font-body text-sm text-sf-text capitalize"
                            >{{ order.refund_status }}</span
                        >
                    </div>
                    <div
                        v-if="order.refunded_at"
                        class="flex justify-between items-center text-sm"
                    >
                        <span class="font-body text-sf-muted">Refunded at</span>
                        <span class="font-body text-sm text-sf-text">{{
                            formatDateTime(order.refunded_at)
                        }}</span>
                    </div>
                </div>
            </div>
            <div
                v-if="isActiveReservation"
                class="bg-sf-ember/10 border border-sf-ember/20 rounded-xl px-5 py-4 flex items-center justify-between gap-4 mb-6"
            >
                <p class="font-body text-sm text-sf-text">
                    Your reservation is pending payment.
                </p>
                <Link
                    :href="checkoutShow({ order: order.uuid })"
                    class="shrink-0 rounded bg-sf-ember px-4 py-2 font-body text-sm text-white hover:bg-sf-ember-hover transition-colors duration-200"
                >
                    Complete checkout
                </Link>
            </div>
            <div
                class="bg-sf-surface border border-sf-border-subtle rounded-xl overflow-hidden transition-colors duration-200"
            >
                <div
                    class="px-5 py-4 border-b border-sf-border-subtle flex items-center gap-3"
                >
                    <span class="h-px w-4 bg-sf-gold" />
                    <h2 class="font-display text-lg font-medium text-sf-text">
                        Tickets
                    </h2>
                </div>
                <div class="divide-y divide-sf-border-subtle">
                    <div
                        v-for="ticket in order.tickets"
                        :key="ticket.uuid"
                        class="px-5 py-5 space-y-4"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p
                                    class="font-body text-sm font-medium text-sf-text"
                                >
                                    {{ ticket.ticket_type!.name }}
                                </p>
                                <p
                                    class="font-code text-xs text-sf-tertiary mt-1"
                                >
                                    {{ ticket.booking_reference }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <span
                                    :class="[
                                        'font-body text-xs px-2.5 py-1 rounded border',
                                        ticketStatusConfig[ticket.status]
                                            ?.classes ??
                                            'text-sf-muted border-sf-border',
                                    ]"
                                >
                                    {{ ticket.status }}
                                </span>
                            </div>
                        </div>
                        <img
                            v-if="ticket.qr_code_url"
                            :src="ticket.qr_code_url"
                            :alt="`QR code for ${ticket.booking_reference}`"
                            class="h-28 w-28 rounded-lg border border-sf-border"
                        />
                    </div>
                </div>
            </div>
        </PageContainer>
        <AlertDialog
            :open="showCancelDialog"
            @update:open="showCancelDialog = $event"
        >
            <AlertDialogContent class="bg-sf-surface border-sf-border">
                <AlertDialogHeader>
                    <AlertDialogTitle class="font-display text-sf-text">
                        Cancel order
                    </AlertDialogTitle>
                    <AlertDialogDescription class="font-body text-sf-muted">
                        Are you sure you want to cancel this order? A refund
                        will be issued if applicable.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel
                        :disabled="cancelForm.processing"
                        class="font-body border-sf-border text-sf-muted bg-transparent hover:bg-sf-surface-raised hover:text-sf-text"
                    >
                        Keep order
                    </AlertDialogCancel>
                    <AlertDialogAction
                        :disabled="cancelForm.processing"
                        class="font-body bg-sf-ember text-white hover:bg-sf-ember-hover"
                        @click="cancelOrder"
                    >
                        Yes, cancel
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </HomeLayout>
</template>
