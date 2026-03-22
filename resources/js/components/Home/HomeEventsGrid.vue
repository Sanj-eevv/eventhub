<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import EventCard from "@/components/Home/EventCard.vue";
import type { PublicEvent } from "@/types/event";
import { index as eventsIndex } from "@/wayfinder/routes/events";

defineProps<{ events: PublicEvent[] }>();
</script>

<template>
    <section class="border-b border-sf-border-subtle">
        <div class="mx-auto max-w-7xl px-5 sm:px-8 py-12 lg:py-20">
            <div class="flex items-center justify-between mb-16">
                <div class="flex items-center gap-4">
                    <span class="h-px w-6 bg-sf-gold" />
                    <p class="font-code text-[10px] tracking-[0.35em] uppercase text-sf-gold">
                        Upcoming Events
                    </p>
                </div>
                <Link
                    :href="eventsIndex()"
                    class="group inline-flex items-center gap-2 font-body text-sm text-sf-muted hover:text-sf-gold transition-colors duration-200"
                >
                    View all
                    <svg class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </Link>
            </div>

            <div v-if="events.length === 0" class="py-20 text-center">
                <p class="font-display text-base font-semibold text-sf-tertiary uppercase tracking-widest mb-2">
                    No events yet
                </p>
                <p class="font-body text-sm text-sf-tertiary/60">
                    Check back soon for upcoming events.
                </p>
            </div>

            <div v-else class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                <EventCard
                    v-for="(event, i) in events.slice(0, 3)"
                    :key="event.uuid"
                    :event="event"
                    :index="i"
                />
            </div>
        </div>
    </section>
</template>
