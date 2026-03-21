<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from "vue";
import { Head, router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
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

    stripeInstance = Stripe(props.stripe_publishable_key);
    const elements = stripeInstance.elements();
    cardElement = elements.create("card");
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
        <div class="mx-auto max-w-2xl px-4 py-10">
            <h1 class="mb-6 text-2xl font-bold">Complete your order</h1>

            <div
                class="mb-4 flex items-center justify-between rounded-md px-4 py-2 text-sm font-medium"
                :class="isExpiringSoon ? 'bg-destructive/10 text-destructive' : 'bg-muted text-muted-foreground'"
            >
                <span>Reservation expires in</span>
                <span class="font-mono text-base">{{ formattedCountdown }}</span>
            </div>

            <Card class="mb-6">
                <CardHeader>
                    <CardTitle>Order Summary</CardTitle>
                </CardHeader>
                <CardContent class="space-y-3">
                    <p class="font-medium">{{ order.event.title }}</p>
                    <div class="divide-y">
                        <div
                            v-for="ticket in order.tickets"
                            :key="ticket.uuid"
                            class="flex items-center justify-between py-2 text-sm"
                        >
                            <span>{{ ticket.ticket_type }}</span>
                            <span class="text-muted-foreground">
                                {{ ticket.booking_reference }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between border-t pt-3 font-semibold">
                        <span>Total</span>
                        <span>{{ order.total_formatted }}</span>
                    </div>
                </CardContent>
            </Card>

            <Card class="mb-6">
                <CardHeader>
                    <CardTitle>Payment Details</CardTitle>
                </CardHeader>
                <CardContent>
                    <div
                        id="card-element"
                        class="rounded-md border border-input bg-background px-3 py-3"
                    />
                    <p
                        v-if="paymentError"
                        class="mt-2 text-sm text-destructive"
                    >
                        {{ paymentError }}
                    </p>
                </CardContent>
            </Card>

            <Button
                class="w-full"
                :disabled="isProcessing || secondsRemaining === 0"
                @click="submitPayment"
            >
                {{ isProcessing ? "Processing..." : `Pay ${order.total_formatted}` }}
            </Button>
        </div>
    </HomeLayout>
</template>
