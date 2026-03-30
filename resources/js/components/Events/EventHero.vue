<script setup lang="ts">
import { formatDate, formatTime } from "@/lib/utils";
import type { EventResource } from "@/types/event";

defineProps<{ event: EventResource }>();
</script>

<template>
    <div class="relative pb-16 border-b border-sf-border-subtle">
        <div class="relative flex flex-col justify-end">
            <div class="flex items-center gap-3 mb-7">
                <span class="h-px w-7 bg-sf-gold shrink-0" />
                <span
                    class="font-body text-[11px] tracking-[0.3em] uppercase text-sf-gold"
                >
                    Live Event
                </span>
            </div>

            <h1
                class="font-display font-semibold text-sf-text leading-[0.92] text-[clamp(2.5rem,6vw,5.5rem)] mb-9"
            >
                {{ event.title }}
            </h1>

            <div class="flex flex-wrap items-center gap-x-6 gap-y-3">
                <div class="flex items-center gap-2.5">
                    <span
                        class="flex h-7 w-7 items-center justify-center rounded-md bg-sf-surface-raised border border-sf-border shrink-0"
                    >
                        <svg
                            class="h-3.5 w-3.5 text-sf-gold"
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
                    </span>
                    <span class="text-sm text-sf-muted">
                        <span class="text-sf-text font-medium">{{
                            formatDate(event.starts_at, event.timezone)
                        }}</span>
                        · {{ formatTime(event.starts_at, event.timezone) }}
                        <span v-if="event.ends_at" class="text-sf-tertiary">
                            - {{ formatDate(event.ends_at, event.timezone) }}
                            {{ formatTime(event.ends_at, event.timezone) }}
                        </span>
                    </span>
                </div>
                <div class="flex items-center gap-2.5">
                    <span
                        class="flex h-7 w-7 items-center justify-center rounded-md bg-sf-surface-raised border border-sf-border shrink-0"
                    >
                        <svg
                            class="h-3.5 w-3.5 text-sf-gold"
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
                    </span>
                    <span class="text-sm text-sf-muted">
                        <span class="text-sf-text font-medium">{{
                            event.venue_name
                        }}</span>
                        <span>, {{ event.address }}</span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
