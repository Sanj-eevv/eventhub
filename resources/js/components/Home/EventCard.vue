<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import type { PublicEvent } from "@/types/event";
import { show as eventShow } from "@/wayfinder/routes/events";

const props = defineProps<{ event: PublicEvent; index: number }>();

const gradients = [
    "bg-gradient-to-br from-[#1a0f2e] via-[#2d1b4e] to-[#0f0a1c]",
    "bg-gradient-to-br from-[#0d1f1a] via-[#1a3d30] to-[#0a1812]",
    "bg-gradient-to-br from-[#1f1209] via-[#3a2410] to-[#150d06]",
    "bg-gradient-to-br from-[#1a1609] via-[#3a2d10] to-[#120f06]",
    "bg-gradient-to-br from-[#0d1520] via-[#1a2d45] to-[#091018]",
    "bg-gradient-to-br from-[#1a0a15] via-[#3a1530] to-[#120a12]",
];

const formatDate = (dateStr: string): string =>
    new Date(dateStr).toLocaleDateString("en-US", {
        day: "numeric",
        month: "short",
        year: "numeric",
    });
</script>

<template>
    <Link
        :href="eventShow({ event: props.event.slug })"
        class="group flex flex-col bg-sf-surface border border-sf-border-subtle hover:border-sf-border rounded-xl overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
    >
        <div class="aspect-[3/2] relative overflow-hidden bg-sf-surface-raised">
            <img
                v-if="props.event.cover_image"
                :src="props.event.cover_image.url"
                :alt="props.event.title"
                class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
            />
            <div
                v-else
                :class="['absolute inset-0', gradients[props.index % gradients.length]]"
            />
        </div>

        <div class="flex flex-col flex-1 p-7 lg:p-8">
            <p class="font-code text-[11px] tracking-[0.2em] uppercase text-sf-gold mb-3">
                {{ formatDate(props.event.starts_at) }}
            </p>
            <h3
                class="font-display font-semibold text-sf-text text-lg leading-snug mb-3 group-hover:text-sf-gold transition-colors duration-200"
            >
                {{ props.event.title }}
            </h3>
            <p
                v-if="props.event.location?.venue_name"
                class="font-body text-sm text-sf-muted mb-5"
            >
                {{ props.event.location.venue_name }}
                <span v-if="props.event.location.address_line_1" class="text-sf-tertiary">
                    · {{ props.event.location.address_line_1 }}
                </span>
            </p>
            <div
                class="mt-auto flex items-center gap-2 text-sf-ember text-sm font-body tracking-wide group-hover:gap-3 transition-all duration-200"
            >
                <span>View event</span>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                </svg>
            </div>
        </div>
    </Link>
</template>
