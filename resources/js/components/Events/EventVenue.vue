<script setup lang="ts">
import type { EventResource } from "@/types/event";

defineProps<{ event: EventResource }>();
</script>

<template>
    <div
        v-if="event.venue_name || event.address"
        class="border-t border-sf-border-subtle pt-10"
    >
        <div class="flex items-center gap-3 mb-6">
            <span class="h-px w-6 bg-sf-gold" />
            <h2 class="font-display text-2xl font-medium text-sf-text">
                Venue
            </h2>
        </div>
        <div
            class="bg-sf-surface border border-sf-border-subtle rounded-xl p-6 space-y-2 transition-colors duration-200"
        >
            <p
                v-if="event.venue_name"
                class="font-display text-lg font-medium text-sf-text"
            >
                {{ event.venue_name }}
            </p>
            <p
                v-if="event.address"
                class="font-body text-sm text-sf-muted"
            >
                {{ event.address }}
            </p>
            <p v-if="event.zip" class="font-body text-sm text-sf-muted">
                {{ event.zip }}
            </p>
            <a
                v-if="event.map_url"
                :href="event.map_url"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-2 text-xs text-sf-gold hover:text-sf-text transition-colors mt-2 font-body"
            >
                Open in Maps
                <svg
                    class="h-3 w-3"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"
                    />
                </svg>
            </a>
        </div>
    </div>
</template>
