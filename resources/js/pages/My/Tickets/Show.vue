<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import PageContainer from "@/components/PageContainer.vue";
import HomeLayout from "@/layouts/HomeLayout.vue";
import type { OrderTicket } from "@/types/order";
import { index as ordersIndex } from "@/wayfinder/routes/orders";

type TicketDetail = OrderTicket & {
    event: { title: string; slug: string };
    ticket_type: string;
};

defineProps<{
    ticket: TicketDetail;
}>();

const statusConfig: Record<string, { label: string; classes: string }> = {
    active: { label: "Active", classes: "text-sf-gold border-sf-gold/40 bg-sf-gold/10" },
    pending: { label: "Pending", classes: "text-sf-muted border-sf-border bg-transparent" },
    used: { label: "Used", classes: "text-sf-tertiary border-sf-border bg-sf-surface-raised" },
    cancelled: { label: "Cancelled", classes: "text-sf-ember border-sf-ember/30 bg-sf-ember/10" },
};
</script>

<template>
    <HomeLayout>
        <Head :title="ticket.booking_reference" />

        <PageContainer>

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

            <!-- Ticket card -->
            <div class="relative rounded-2xl overflow-hidden border border-sf-border shadow-2xl shadow-black/30 transition-colors duration-200">

                <!-- Top half -->
                <div class="bg-sf-surface px-7 pt-8 pb-7 transition-colors duration-200">
                    <h1 class="font-display font-semibold text-2xl text-sf-text leading-tight text-center mb-1">
                        {{ ticket.event.title }}
                    </h1>
                    <p class="font-body text-xs text-sf-muted text-center tracking-wide">
                        {{ ticket.ticket_type.name }}
                    </p>
                </div>

                <!-- Perforated divider -->
                <div class="relative bg-sf-bg flex items-center transition-colors duration-200">
                    <!-- Left notch -->
                    <div class="absolute -left-4 h-8 w-8 rounded-full bg-sf-bg border border-sf-border z-10 transition-colors duration-200" />
                    <!-- Dashed line -->
                    <div class="flex-1 mx-6 border-t border-dashed border-sf-border" />
                    <!-- Right notch -->
                    <div class="absolute -right-4 h-8 w-8 rounded-full bg-sf-bg border border-sf-border z-10 transition-colors duration-200" />
                </div>

                <!-- Bottom half: QR + details -->
                <div class="bg-sf-surface px-7 pt-7 pb-8 flex flex-col items-center gap-6 transition-colors duration-200">

                    <!-- QR code — white bg always for readability -->
                    <div v-if="ticket.qr_code_path" class="p-3 bg-white rounded-xl">
                        <img
                            :src="ticket.qr_code_path"
                            :alt="`QR code for ${ticket.booking_reference}`"
                            class="h-48 w-48 block"
                        />
                    </div>
                    <div
                        v-else
                        class="h-52 w-52 flex flex-col items-center justify-center rounded-xl border border-dashed border-sf-border text-center gap-2"
                    >
                        <svg class="h-8 w-8 text-sf-border" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                        </svg>
                        <p class="font-body text-xs text-sf-tertiary">QR code generating…</p>
                    </div>

                    <!-- Booking reference -->
                    <div class="text-center space-y-1.5">
                        <p class="font-code text-2xl font-medium tracking-[0.2em] text-sf-text">
                            {{ ticket.booking_reference }}
                        </p>
                        <p v-if="ticket.attendee_name" class="font-body text-sm text-sf-muted">
                            {{ ticket.attendee_name }}
                        </p>
                    </div>

                    <!-- Status badge -->
                    <span
                        :class="[
                            'font-body text-xs px-3 py-1.5 rounded-full border',
                            statusConfig[ticket.status]?.classes ?? 'text-sf-muted border-sf-border',
                        ]"
                    >
                        ● {{ statusConfig[ticket.status]?.label ?? ticket.status }}
                    </span>
                </div>
            </div>

            <p class="font-body text-xs text-sf-tertiary text-center mt-6">
                Present this QR code at the venue entrance.
            </p>
        </PageContainer>
    </HomeLayout>
</template>
