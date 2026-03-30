<script setup lang="ts">
import { useTemplateRef } from "vue";
import { useStripePayment } from "@/composables/checkout/useStripePayment";

const props = defineProps<{
    publishableKey: string;
    clientSecret: string;
    total: string;
    disabled: boolean;
}>();

const emit = defineEmits<{
    succeeded: [];
}>();

const cardElementRef = useTemplateRef<HTMLElement>("cardElement");

const { paymentError, isProcessing, submitPayment } = useStripePayment(
    props.publishableKey,
    props.clientSecret,
    cardElementRef,
);

async function handleSubmit(): Promise<void> {
    const succeeded = await submitPayment();
    if (succeeded) emit("succeeded");
}
</script>

<template>
    <div>
        <div
            class="bg-sf-surface border border-sf-border-subtle rounded-xl overflow-hidden mb-6 transition-colors duration-200"
        >
            <div class="px-5 py-4 border-b border-sf-border-subtle">
                <h2 class="font-display text-lg font-medium text-sf-text">
                    Payment Details
                </h2>
            </div>
            <div class="px-5 py-4">
                <div
                    ref="cardElement"
                    class="bg-sf-surface-raised border border-sf-border rounded-lg px-4 py-3.5 transition-colors duration-200"
                />
                <p
                    v-if="paymentError"
                    class="mt-3 font-body text-sm text-sf-ember"
                >
                    {{ paymentError }}
                </p>
            </div>
        </div>

        <button
            type="button"
            class="w-full py-4 rounded bg-sf-ember text-white font-body text-sm tracking-wide hover:bg-sf-ember-hover active:scale-[0.99] transition-all duration-200 disabled:opacity-40 disabled:pointer-events-none"
            :disabled="isProcessing || disabled"
            @click="handleSubmit"
        >
            <span
                v-if="isProcessing"
                class="flex items-center justify-center gap-2"
            >
                <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    />
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                    />
                </svg>
                Processing…
            </span>
            <span v-else>Pay {{ total }}</span>
        </button>

        <p class="mt-4 font-body text-xs text-center text-sf-tertiary">
            Secured by Stripe · 256-bit encryption
        </p>
    </div>
</template>
