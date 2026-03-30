<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { useReservationCountdown } from "@/composables/checkout/useReservationCountdown";
import type { ActiveOrderResource } from "@/types/event";
import { show as checkoutShow } from "@/wayfinder/routes/checkout";

const props = defineProps<{
    activeOrder: ActiveOrderResource;
}>();

const { formattedCountdown, isActive } = useReservationCountdown(
    props.activeOrder.expires_at,
);
</script>

<template>
    <div
        v-if="isActive"
        class="fixed top-16 left-0 right-0 z-40 bg-sf-ember/10 backdrop-blur-md border-b border-sf-ember/20"
    >
        <div
            class="mx-auto max-w-7xl px-5 sm:px-8 py-3 flex items-center justify-between gap-4"
        >
            <p class="font-body text-sm text-sf-text">
                You have an active reservation — expires in
                <span class="font-medium tabular-nums text-sf-ember">{{
                    formattedCountdown
                }}</span>
            </p>
            <Link
                :href="checkoutShow({ order: activeOrder.uuid })"
                class="shrink-0 rounded bg-sf-ember px-4 py-1.5 font-body text-xs text-white tracking-wide hover:bg-sf-ember-hover transition-colors duration-200"
            >
                Resume checkout
            </Link>
        </div>
    </div>
</template>
