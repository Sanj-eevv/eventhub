import { onMounted, shallowRef } from "vue";
import type { Ref } from "vue";

declare const Stripe: (key: string) => any;

export function useStripePayment(
    publishableKey: string,
    clientSecret: string,
    mountRef: Ref<HTMLElement | null>,
) {
    const paymentError = shallowRef<string | null>(null);
    const isProcessing = shallowRef(false);

    let stripeInstance: ReturnType<typeof Stripe> | null = null;
    let cardElement: any = null;

    onMounted(() => {
        if (!mountRef.value) return;

        const isDark = document.documentElement.classList.contains("dark");

        stripeInstance = Stripe(publishableKey);
        const elements = stripeInstance.elements();

        cardElement = elements.create("card", {
            style: {
                base: {
                    color: isDark ? "#ece8de" : "#1c1814",
                    fontFamily: "'DM Sans', system-ui, sans-serif",
                    fontSize: "14px",
                    "::placeholder": {
                        color: isDark ? "#4a4840" : "#9a8b7e",
                    },
                },
                invalid: { color: isDark ? "#d45a2a" : "#bc4520" },
            },
        });

        cardElement.mount(mountRef.value);
    });

    async function submitPayment(): Promise<boolean> {
        if (!stripeInstance || !cardElement || isProcessing.value) return false;

        isProcessing.value = true;
        paymentError.value = null;

        const { error, paymentIntent } =
            await stripeInstance.confirmCardPayment(clientSecret, {
                payment_method: { card: cardElement },
            });

        if (error) {
            paymentError.value = error.message ?? "Payment failed.";
            isProcessing.value = false;
            return false;
        }

        return paymentIntent?.status === "succeeded";
    }

    return { paymentError, isProcessing, submitPayment };
}
