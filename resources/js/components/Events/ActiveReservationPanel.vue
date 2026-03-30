<script setup lang="ts">
import { router, Link } from "@inertiajs/vue3";
import { watch } from "vue";
import { useReservationCountdown } from "@/composables/checkout/useReservationCountdown";
import type { ActiveOrderResource } from "@/types/event";
import { show as checkoutShow } from "@/wayfinder/routes/checkout";

const props = defineProps<{
    activeOrder: ActiveOrderResource;
}>();

const { formattedCountdown, isExpired } = useReservationCountdown(
    props.activeOrder.expires_at,
);

watch(isExpired, (expired) => {
    if (expired) router.reload();
});
</script>

<template>
    <div
        class="sticky top-24 bg-sf-surface border border-sf-border-subtle rounded-xl p-8 text-center space-y-5"
    >
        <div
            class="inline-flex h-14 w-14 items-center justify-center rounded-full border border-sf-ember/30 bg-sf-ember/10"
        >
            <svg
                class="h-6 w-6 text-sf-ember"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="1.5"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z"
                />
            </svg>
        </div>
        <div class="space-y-2">
            <p class="font-display text-lg font-medium text-sf-text">
                Reservation in progress
            </p>
            <p class="font-body text-sm text-sf-muted">
                You have an active reservation pending payment. Complete your
                checkout before selecting new tickets.
            </p>
        </div>
        <p class="font-code text-2xl font-medium text-sf-text tracking-widest">
            {{ formattedCountdown }}
        </p>
        <div class="space-y-3 pt-1">
            <Link
                :href="checkoutShow({ order: activeOrder.uuid })"
                class="block w-full py-3 rounded bg-sf-ember text-white font-body text-sm tracking-wide hover:bg-sf-ember-hover transition-colors duration-200 text-center"
            >
                Complete checkout
            </Link>
        </div>
    </div>
</template>
