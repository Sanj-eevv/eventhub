<script setup lang="ts">
import { formatDate, formatTime } from "@/lib/utils";
import type { EventResource } from "@/types/event";

defineProps<{ event: EventResource }>();
</script>

<template>
    <div class="relative border-b border-sf-border-subtle">
        <div
            v-if="event.cover_image"
            class="relative h-[45vw] max-h-140 min-h-65 overflow-hidden bg-sf-surface"
        >
            <img
                :src="event.cover_image.url"
                :alt="event.title"
                class="h-full w-full object-cover"
            />
            <div
                class="absolute inset-0 bg-linear-to-t from-sf-bg via-sf-bg/40 to-transparent"
            />
            <div
                class="absolute bottom-0 left-0 right-0 mx-auto max-w-7xl px-5 sm:px-8 pb-10 lg:pb-14"
            >
                <p
                    class="font-body text-xs tracking-[0.25em] uppercase text-sf-gold mb-3"
                >
                    Live Event
                </p>
                <h1
                    class="font-display font-semibold text-[clamp(2rem,5vw,4.5rem)] text-sf-text leading-[0.95]"
                >
                    {{ event.title }}
                </h1>
                <div class="mt-4 flex flex-wrap items-center gap-6 text-sm">
                    <span class="flex items-center gap-2 text-sf-muted">
                        <svg
                            class="h-4 w-4 text-sf-gold shrink-0"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.75"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"
                            />
                        </svg>
                        {{ formatDate(event.starts_at, event.timezone) }} ·
                        {{ formatTime(event.starts_at, event.timezone) }}
                    </span>
                    <span v-if="event.ends_at" class="text-sf-tertiary"
                        >— {{ formatTime(event.ends_at, event.timezone) }}</span
                    >
                    <span
                        v-if="event.venue_name"
                        class="flex items-center gap-2 text-sf-muted"
                    >
                        <svg
                            class="h-4 w-4 text-sf-gold shrink-0"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.75"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"
                            />
                        </svg>
                        {{ event.venue_name }}
                        <span v-if="event.address" class="text-sf-tertiary"
                            >, {{ event.address }}</span
                        >
                    </span>
                </div>
            </div>
        </div>
        <div
            v-else
            class="bg-linear-to-b from-sf-surface to-sf-bg transition-colors duration-200"
        >
            <div
                class="absolute inset-0 bg-[radial-gradient(ellipse_70%_50%_at_50%_0%,color-mix(in_srgb,var(--sf-gold)_6%,transparent),transparent)]"
            />
            <div class="relative mx-auto max-w-7xl px-5 sm:px-8 py-16 lg:py-24">
                <div class="flex items-start gap-3 mb-6">
                    <span class="h-px w-6 bg-sf-gold mt-3 shrink-0" />
                    <div>
                        <p
                            class="font-body text-xs tracking-[0.25em] uppercase text-sf-gold mb-4"
                        >
                            Live Event
                        </p>
                        <h1
                            class="font-display font-semibold text-[clamp(2.5rem,6vw,5.5rem)] text-sf-text leading-[0.95]"
                        >
                            {{ event.title }}
                        </h1>
                    </div>
                </div>
                <div class="ml-9 flex flex-wrap items-center gap-6 text-sm">
                    <span class="flex items-center gap-2 text-sf-muted">
                        <svg
                            class="h-4 w-4 text-sf-gold shrink-0"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.75"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"
                            />
                        </svg>
                        {{ formatDate(event.starts_at, event.timezone) }} ·
                        {{ formatTime(event.starts_at, event.timezone) }}
                    </span>
                    <span v-if="event.ends_at" class="text-sf-tertiary">
                        {{ formatDate(event.ends_at, event.timezone) }} ·
                        {{ formatTime(event.ends_at, event.timezone) }}</span
                    >
                    <span
                        v-if="event.venue_name"
                        class="flex items-center gap-2 text-sf-muted"
                    >
                        <svg
                            class="h-4 w-4 text-sf-gold shrink-0"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.75"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"
                            />
                        </svg>
                        {{ event.venue_name }},
                        <span v-if="event.address" class="text-sf-tertiary">{{
                            event.address
                        }}</span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
