<script setup lang="ts">
import { Head, InfiniteScroll } from "@inertiajs/vue3";
import EventCard from "@/components/Home/EventCard.vue";
import HomeLayout from "@/layouts/HomeLayout.vue";
import type { PaginatedResponse } from "@/types";
import type { EventResource } from "@/types/event";

defineProps<{
    events: PaginatedResponse<EventResource>;
}>();
</script>

<template>
    <HomeLayout>
        <Head title="Browse Events" />

        <div class="mx-auto max-w-7xl px-5 sm:px-8 py-16">
            <div class="mb-12 flex items-end justify-between">
                <div>
                    <p
                        class="font-body text-xs tracking-[0.3em] uppercase text-sf-gold mb-3"
                    >
                        All Events
                    </p>
                    <h1
                        class="font-display font-semibold text-[clamp(2.5rem,5vw,4rem)] text-sf-text leading-none"
                    >
                        What's On
                    </h1>
                </div>
                <p
                    v-if="events.meta.total"
                    class="hidden sm:block font-code text-sm text-sf-tertiary"
                >
                    {{ events.meta.total }} events
                </p>
            </div>

            <div v-if="events.data.length === 0" class="py-32 text-center">
                <div
                    class="inline-flex h-16 w-16 items-center justify-center rounded-full border border-sf-border mb-6"
                >
                    <svg
                        class="h-7 w-7 text-sf-tertiary"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.5"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"
                        />
                    </svg>
                </div>
                <p class="font-display text-xl font-medium text-sf-tertiary">
                    No events scheduled yet
                </p>
                <p class="font-body text-sm text-sf-tertiary mt-2">
                    Check back soon for upcoming events.
                </p>
            </div>

            <InfiniteScroll v-else data="events">
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 lg:gap-8">
                    <EventCard
                        v-for="(event, index) in events.data"
                        :key="event.uuid"
                        :event="event"
                        :index="index"
                    />
                </div>

                <template #loading>
                    <div
                        class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 lg:gap-8"
                    >
                        <div
                            v-for="i in 3"
                            :key="i"
                            class="rounded-xl overflow-hidden border border-sf-border"
                        >
                            <div
                                class="aspect-3/2 animate-pulse bg-sf-surface"
                            />
                            <div
                                class="bg-sf-surface border-t border-sf-border-subtle p-7"
                            >
                                <div
                                    class="h-2.5 w-24 rounded animate-pulse bg-sf-border mb-3"
                                />
                                <div
                                    class="h-4 w-3/4 rounded animate-pulse bg-sf-border mb-2"
                                />
                                <div
                                    class="h-3 w-1/2 rounded animate-pulse bg-sf-border"
                                />
                            </div>
                        </div>
                    </div>
                </template>
            </InfiniteScroll>
        </div>
    </HomeLayout>
</template>
