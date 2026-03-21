<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from "vue";
import { Head, router } from "@inertiajs/vue3";
import HomeLayout from "@/layouts/HomeLayout.vue";
import type { Order } from "@/types/event";
import { show as eventShow } from "@/wayfinder/routes/events";
import { pay } from "@/wayfinder/routes/checkout";

declare const Stripe: any;

const props = defineProps<{
    order: Order;
    stripe_publishable_key: string;
}>();

const secondsRemaining = ref(0);
const paymentError = ref<string | null>(null);
const isProcessing = ref(false);

let stripeInstance: any = null;
let cardElement: any = null;
let countdownInterval: ReturnType<typeof setInterval> | null = null;

const formattedCountdown = computed(() => {
    const minutes = Math.floor(secondsRemaining.value / 60);
    const seconds = secondsRemaining.value % 60;
    return `${String(minutes).padStart(2, "0")}:${String(seconds).padStart(2, "0")}`;
});

const isExpiringSoon = computed(() => secondsRemaining.value < 120 && secondsRemaining.value > 0);

const timerProgress = computed(() => {
    const total = 10 * 60;
    return secondsRemaining.value / total;
});

function startCountdown(): void {
    if (!props.order.expires_at) {
        return;
    }
    const expiresAt = new Date(props.order.expires_at).getTime();
    countdownInterval = setInterval(() => {
        const now = Date.now();
        const remaining = Math.max(0, Math.floor((expiresAt - now) / 1000));
        secondsRemaining.value = remaining;
        if (remaining === 0) {
            clearInterval(countdownInterval!);
            router.visit(eventShow({ event: props.order.event.slug }).url);
        }
    }, 1000);
}

onMounted(() => {
    startCountdown();

    const isDark = document.documentElement.classList.contains("dark");
    stripeInstance = Stripe(props.stripe_publishable_key);
    const elements = stripeInstance.elements();
    cardElement = elements.create("card", {
        style: {
            base: {
                color: isDark ? "#ece8de" : "#1c1814",
                fontFamily: "'DM Sans', system-ui, sans-serif",
                fontSize: "14px",
                "::placeholder": { color: isDark ? "#4a4840" : "#9a8b7e" },
            },
            invalid: { color: isDark ? "#d45a2a" : "#bc4520" },
        },
    });
    cardElement.mount("#card-element");
});

onUnmounted(() => {
    if (countdownInterval) {
        clearInterval(countdownInterval);
    }
});

async function submitPayment(): Promise<void> {
    if (!stripeInstance || !cardElement || isProcessing.value) {
        return;
    }

    isProcessing.value = true;
    paymentError.value = null;

    const { error, paymentIntent } = await stripeInstance.confirmCardPayment(
        (props.order as any).stripe_client_secret,
        { payment_method: { card: cardElement } },
    );

    if (error) {
        paymentError.value = error.message ?? "Payment failed.";
        isProcessing.value = false;
        return;
    }

    if (paymentIntent?.status === "succeeded") {
        router.post(pay({ order: props.order.uuid }).url, {}, {
            onFinish: () => {
                isProcessing.value = false;
            },
        });
    }
}
</script>

<template>
    <HomeLayout>
        <Head title="Checkout" />

        <div class="mx-auto max-w-xl px-5 sm:px-8 py-16">

            <div class="mb-10">
                <p class="font-body text-xs tracking-[0.3em] uppercase text-sf-gold mb-3">Secure Checkout</p>
                <h1 class="font-display font-semibold text-3xl text-sf-text">Complete your order</h1>
            </div>

            <!-- Countdown -->
            <div
                :class="[
                    'rounded-xl border px-5 py-4 mb-3 flex items-center justify-between transition-all duration-500',
                    isExpiringSoon ? 'border-sf-ember/40 bg-sf-ember/10' : 'border-sf-border bg-sf-surface',
                ]"
            >
                <div class="flex items-center gap-3">
                    <div :class="['h-2 w-2 rounded-full transition-colors', isExpiringSoon ? 'bg-sf-ember animate-pulse' : 'bg-sf-gold']" />
                    <span class="font-body text-sm text-sf-muted">Reservation expires in</span>
                </div>
                <span :class="['font-code text-xl tabular-nums transition-colors', isExpiringSoon ? 'text-sf-ember' : 'text-sf-text']">
                    {{ formattedCountdown }}
                </span>
            </div>

            <!-- Progress bar -->
            <div class="h-px bg-sf-border-subtle mb-8 overflow-hidden rounded-full">
                <div
                    class="h-full transition-all duration-1000 rounded-full"
                    :class="isExpiringSoon ? 'bg-sf-ember' : 'bg-sf-gold'"
                    :style="{ width: `${timerProgress * 100}%` }"
                />
            </div>

            <!-- Order summary -->
            <div class="bg-sf-surface border border-sf-border-subtle rounded-xl overflow-hidden mb-6 transition-colors duration-200">
                <div class="px-5 py-4 border-b border-sf-border-subtle">
                    <h2 class="font-display text-lg font-medium text-sf-text">Order Summary</h2>
                </div>
                <div class="px-5 py-4">
                    <p class="font-body text-sm font-medium text-sf-text mb-4">{{ order.event.title }}</p>
                    <div class="space-y-2">
                        <div v-for="ticket in order.tickets" :key="ticket.uuid" class="flex items-center justify-between text-sm">
                            <span class="font-body text-sf-muted">{{ ticket.ticket_type }}</span>
                            <span class="font-code text-xs text-sf-tertiary">{{ ticket.booking_reference }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-5 pt-4 border-t border-sf-border-subtle">
                        <span class="font-body text-sm text-sf-muted">Total</span>
                        <span class="font-display text-xl font-semibold text-sf-text">{{ order.total_formatted }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment details -->
            <div class="bg-sf-surface border border-sf-border-subtle rounded-xl overflow-hidden mb-6 transition-colors duration-200">
                <div class="px-5 py-4 border-b border-sf-border-subtle">
                    <h2 class="font-display text-lg font-medium text-sf-text">Payment Details</h2>
                </div>
                <div class="px-5 py-4">
                    <div
                        id="card-element"
                        class="bg-sf-surface-raised border border-sf-border rounded-lg px-4 py-3.5 transition-colors duration-200"
                    />
                    <p v-if="paymentError" class="mt-3 font-body text-sm text-sf-ember">{{ paymentError }}</p>
                </div>
            </div>

            <!-- Submit -->
            <button
                type="button"
                class="w-full py-4 rounded bg-sf-ember text-white font-body text-sm tracking-wide hover:bg-sf-ember-hover active:scale-[0.99] transition-all duration-200 disabled:opacity-40 disabled:pointer-events-none"
                :disabled="isProcessing || secondsRemaining === 0"
                @click="submitPayment"
            >
                <span v-if="isProcessing" class="flex items-center justify-center gap-2">
                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                    Processing…
                </span>
                <span v-else>Pay {{ order.total_formatted }}</span>
            </button>

            <p class="mt-4 font-body text-xs text-center text-sf-tertiary">
                Secured by Stripe · 256-bit encryption
            </p>
        </div>
    </HomeLayout>
</template>
