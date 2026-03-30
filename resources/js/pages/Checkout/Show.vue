<script setup lang="ts">
import { Head, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import CheckoutCountdown from "@/components/Checkout/CheckoutCountdown.vue";
import CheckoutOrderSummary from "@/components/Checkout/CheckoutOrderSummary.vue";
import CheckoutPaymentForm from "@/components/Checkout/CheckoutPaymentForm.vue";
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
import { useReservationCountdown } from "@/composables/checkout/useReservationCountdown";
import HomeLayout from "@/layouts/HomeLayout.vue";
import { formatCurrency } from "@/lib/utils";
import type { Order } from "@/types/order";
import { cancel, pay } from "@/wayfinder/routes/checkout";
import { show as eventShow } from "@/wayfinder/routes/events";

const props = defineProps<{
    order: Order;
    client_secret: string;
    stripe_publishable_key: string;
}>();

const { formattedCountdown, isExpiringSoon, isExpired, timerProgress } =
    useReservationCountdown(props.order.expires_at!, props.order.reserved_at!);

const cancelling = ref(false);
const showCancelDialog = ref(false);

watch(isExpired, (expired) => {
    if (expired) router.visit(eventShow({ event: props.order.event.slug }).url);
});

function onPaymentSucceeded(): void {
    router.post(pay({ order: props.order.uuid }).url, {});
}

function confirmCancel(): void {
    cancelling.value = true;
    router.delete(cancel({ order: props.order.uuid }).url, {
        onFinish: () => (cancelling.value = false),
    });
}
</script>

<template>
    <HomeLayout>
        <Head title="Checkout" />

        <PageContainer>
            <div class="mb-10">
                <p
                    class="font-body text-xs tracking-[0.3em] uppercase text-sf-gold mb-3"
                >
                    Secure Checkout
                </p>
                <h1 class="font-display font-semibold text-3xl text-sf-text">
                    Complete your order
                </h1>
            </div>

            <CheckoutCountdown
                :formatted-countdown="formattedCountdown"
                :is-expiring-soon="isExpiringSoon"
                :timer-progress="timerProgress"
            />

            <CheckoutOrderSummary :order="order" />

            <CheckoutPaymentForm
                :publishable-key="stripe_publishable_key"
                :client-secret="client_secret"
                :total="formatCurrency(order.total)"
                :disabled="isExpired"
                @succeeded="onPaymentSucceeded"
            />

            <div class="mt-4">
                <button
                    type="button"
                    :disabled="cancelling"
                    class="w-full py-3 rounded border border-sf-border text-sf-muted font-body text-sm hover:border-sf-ember hover:text-sf-ember transition-all duration-200 disabled:opacity-50"
                    @click="showCancelDialog = true"
                >
                    {{ cancelling ? "Cancelling…" : "Cancel reservation" }}
                </button>
            </div>
        </PageContainer>

        <AlertDialog :open="showCancelDialog" @update:open="showCancelDialog = $event">
            <AlertDialogContent class="bg-sf-surface border-sf-border">
                <AlertDialogHeader>
                    <AlertDialogTitle class="font-display text-sf-text">
                        Cancel reservation
                    </AlertDialogTitle>
                    <AlertDialogDescription class="font-body text-sf-muted">
                        Are you sure you want to cancel? Your seats will be
                        released and this reservation cannot be recovered.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel
                        :disabled="cancelling"
                        class="font-body border-sf-border text-sf-muted bg-transparent hover:bg-sf-surface-raised hover:text-sf-text"
                    >
                        Keep reservation
                    </AlertDialogCancel>
                    <AlertDialogAction
                        :disabled="cancelling"
                        class="font-body bg-sf-ember text-white hover:bg-sf-ember-hover"
                        @click="confirmCancel"
                    >
                        Yes, cancel
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </HomeLayout>
</template>
