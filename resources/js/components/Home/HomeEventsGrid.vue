<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import type { PublicEvent } from "@/types/event";
import { index as eventsIndex, show as eventShow } from "@/wayfinder/routes/events";

defineProps<{ events: PublicEvent[] }>();

const formatDate = (dateStr: string): string => {
    const date = new Date(dateStr);
    return date.toLocaleDateString("en-US", {
        day: "numeric",
        month: "short",
        year: "numeric",
    });
};
</script>

<template>
    <section class="border-b border-sf-border-subtle">
        <div class="mx-auto max-w-7xl px-5 sm:px-8 py-12 lg:py-20">
            <div class="flex items-center justify-between mb-16">
                <div class="flex items-center gap-4">
                    <span class="h-px w-6 bg-sf-gold" />
                    <p
                        class="font-code text-[10px] tracking-[0.35em] uppercase text-sf-gold"
                    >
                        Upcoming Events
                    </p>
                </div>
                <Link
                    :href="eventsIndex()"
                    class="group inline-flex items-center gap-2 font-body text-sm text-sf-muted hover:text-sf-gold transition-colors duration-200"
                >
                    View all
                    <svg
                        class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-0.5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"
                        />
                    </svg>
                </Link>
            </div>

            <div v-if="events.length === 0" class="py-20 text-center">
                <p
                    class="font-display text-base font-semibold text-sf-tertiary uppercase tracking-widest mb-2"
                >
                    No events yet
                </p>
                <p class="font-body text-sm text-sf-tertiary/60">
                    Check back soon for upcoming events.
                </p>
            </div>

            <div
                v-else
                class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8"
            >
                <Link
                    v-for="(event, i) in events.slice(0, 3)"
                    :key="event.uuid"
                    :href="eventShow({ event: event.slug })"
                    class="group flex flex-col bg-sf-surface border border-sf-border-subtle hover:border-sf-border rounded-xl overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
                >
                    <div
                        class="aspect-[3/2] relative overflow-hidden bg-sf-surface-raised"
                    >
                        <img
                            v-if="event.cover_image"
                            :src="event.cover_image.url"
                            :alt="event.title"
                            class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                        />
                        <div
                            v-else
                            :class="[
                                'absolute inset-0',
                                [
                                    'bg-gradient-to-br from-[#1a0f2e] via-[#2d1b4e] to-[#0f0a1c]',
                                    'bg-gradient-to-br from-[#0d1f1a] via-[#1a3d30] to-[#0a1812]',
                                    'bg-gradient-to-br from-[#1f1209] via-[#3a2410] to-[#150d06]',
                                ][i % 3],
                            ]"
                        />
                    </div>

                    <div class="flex flex-col flex-1 p-7 lg:p-8">
                        <p
                            class="font-code text-[11px] tracking-[0.2em] uppercase text-sf-gold mb-3"
                        >
                            {{ formatDate(event.starts_at) }}
                        </p>
                        <h3
                            class="font-display font-semibold text-sf-text text-lg leading-snug mb-3 group-hover:text-sf-gold transition-colors duration-200"
                        >
                            {{ event.title }}
                        </h3>
                        <p
                            v-if="event.location?.venue_name"
                            class="font-body text-sm text-sf-muted mb-5"
                        >
                            {{ event.location.venue_name }}
                            <span
                                v-if="event.location.address_line_1"
                                class="text-sf-tertiary"
                            >
                                · {{ event.location.address_line_1 }}</span
                            >
                        </p>
                        <div
                            class="mt-auto flex items-center gap-2 text-sf-ember text-sm font-body tracking-wide group-hover:gap-3 transition-all duration-200"
                        >
                            <span>View event</span>
                            <svg
                                class="h-4 w-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"
                                />
                            </svg>
                        </div>
                    </div>
                </Link>
            </div>
        </div>
    </section>
</template>
