<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import HomeLayout from "@/layouts/HomeLayout.vue";
import type { PaginatedResponse } from "@/types";
import type { PublicEvent } from "@/types/event";
import { show } from "@/wayfinder/routes/events";

defineProps<{
    events: PaginatedResponse<PublicEvent>;
}>();

const cardGradients = [
    "from-[#1a0f2e] via-[#2d1b4e] to-[#0f0a1c]",
    "from-[#0d1f1a] via-[#1a3d30] to-[#0a1812]",
    "from-[#1f0d0d] via-[#3d1a1a] to-[#150a0a]",
    "from-[#1a1609] via-[#3a2d10] to-[#120f06]",
    "from-[#0d1520] via-[#1a2d45] to-[#091018]",
    "from-[#1a0a15] via-[#3a1530] to-[#120a12]",
] as const;

const formatDate = (dateStr: string): string => {
    const d = new Date(dateStr);
    return d.toLocaleDateString("en-US", { day: "2-digit", month: "short", year: "numeric" });
};

const formatShortDate = (dateStr: string): string => {
    const d = new Date(dateStr);
    return d.toLocaleDateString("en-US", { weekday: "short", day: "numeric", month: "short" }).toUpperCase();
};
</script>

<template>
    <HomeLayout>
        <Head title="Browse Events" />

        <div class="mx-auto max-w-7xl px-5 sm:px-8 py-16">

            <!-- Header -->
            <div class="mb-12 flex items-end justify-between">
                <div>
                    <p class="font-body text-xs tracking-[0.3em] uppercase text-sf-gold mb-3">All Events</p>
                    <h1 class="font-display font-semibold text-[clamp(2.5rem,5vw,4rem)] text-sf-text leading-none">
                        What's On
                    </h1>
                </div>
                <p v-if="events.meta.total" class="hidden sm:block font-code text-sm text-sf-tertiary">
                    {{ events.meta.total }} events
                </p>
            </div>

            <!-- Empty state -->
            <div v-if="events.data.length === 0" class="py-32 text-center">
                <div class="inline-flex h-16 w-16 items-center justify-center rounded-full border border-sf-border mb-6">
                    <svg class="h-7 w-7 text-sf-tertiary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                </div>
                <p class="font-display text-xl font-medium text-sf-tertiary">No events scheduled yet</p>
                <p class="font-body text-sm text-sf-tertiary mt-2">Check back soon for upcoming events.</p>
            </div>

            <!-- Event grid -->
            <div v-else class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="(event, index) in events.data"
                    :key="event.uuid"
                    :href="show({ event: event.slug })"
                    class="group relative flex flex-col rounded-xl overflow-hidden border border-sf-border hover:border-sf-muted/50 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
                >
                    <!-- Poster background — always dark, editorial poster aesthetic -->
                    <div :class="['aspect-[2/3] relative bg-gradient-to-br', cardGradients[index % cardGradients.length]]">
                        <!-- Date chip -->
                        <div class="absolute top-4 right-4">
                            <span class="font-code text-[10px] tracking-widest text-[#c9a55a] bg-black/40 backdrop-blur-sm px-2.5 py-1 rounded">
                                {{ formatShortDate(event.starts_at) }}
                            </span>
                        </div>
                        <!-- Decorative lines -->
                        <div class="absolute top-1/3 left-0 right-0 px-6 opacity-20 group-hover:opacity-30 transition-opacity">
                            <div class="h-px w-full bg-[#c9a55a]" />
                            <div class="h-px w-2/3 bg-[#c9a55a] mt-3" />
                        </div>
                        <!-- Bottom overlay -->
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-5 pt-14">
                            <h2 class="font-display font-semibold text-xl text-white leading-tight">
                                {{ event.title }}
                            </h2>
                            <p v-if="event.location?.venue_name" class="font-body text-xs text-white/60 mt-1.5 tracking-wide">
                                {{ event.location.venue_name }}<span v-if="event.location.address_line_1">, {{ event.location.address_line_1 }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Card footer -->
                    <div class="bg-sf-surface border-t border-sf-border-subtle px-5 py-3.5 flex items-center justify-between transition-colors duration-200">
                        <p class="font-body text-xs text-sf-muted">
                            {{ formatDate(event.starts_at) }}
                            <span v-if="event.ends_at"> — {{ formatDate(event.ends_at) }}</span>
                        </p>
                        <span class="font-body text-xs text-sf-ember tracking-wide group-hover:translate-x-0.5 transition-transform inline-flex items-center gap-1">
                            View
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </span>
                    </div>
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="events.meta.last_page > 1" class="mt-12 flex justify-center gap-2">
                <Link
                    v-for="link in events.meta.links"
                    :key="link.label"
                    :href="link.url ?? ''"
                    :class="[
                        'inline-flex items-center justify-center h-9 min-w-9 px-3 rounded border text-sm font-body transition-all duration-200',
                        link.active
                            ? 'border-sf-gold text-sf-gold bg-sf-gold/10'
                            : link.url
                                ? 'border-sf-border text-sf-muted hover:border-sf-muted hover:text-sf-text'
                                : 'border-sf-border-subtle text-sf-tertiary cursor-not-allowed pointer-events-none',
                    ]"
                    v-html="link.label"
                />
            </div>
        </div>
    </HomeLayout>
</template>
