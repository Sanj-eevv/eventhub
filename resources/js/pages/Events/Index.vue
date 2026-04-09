<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import { Head, InfiniteScroll } from "@inertiajs/vue3";
import { refDebounced } from "@vueuse/core";
import { shallowRef, watch } from "vue";
import EventCard from "@/components/Home/EventCard.vue";
import EventCardSkeleton from "@/components/Home/EventCardSkeleton.vue";
import EventsEmptyState from "@/components/Home/EventsEmptyState.vue";
import PageContainer from "@/components/PageContainer.vue";
import HomeLayout from "@/layouts/HomeLayout.vue";
import type { PaginatedResponse } from "@/types";
import type { EventResource } from "@/types/event";
import { index as eventsIndex } from "@/wayfinder/routes/events";

const props = defineProps<{
    events: PaginatedResponse<EventResource>;
    filters: { search: string; upcoming: boolean };
}>();

const search = shallowRef(props.filters.search);
const upcoming = shallowRef(props.filters.upcoming);
const debouncedSearch = refDebounced(search, 300);

watch([debouncedSearch, upcoming], () => {
    router.get(
        eventsIndex().url,
        { search: debouncedSearch.value, upcoming: upcoming.value },
        { replace: true, preserveScroll: false },
    );
});
</script>

<template>
    <HomeLayout>
        <Head title="Browse Events" />

        <PageContainer>
            <div class="mb-10 flex items-end justify-between">
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

            <div class="flex items-center gap-3 mb-10">
                <div class="relative flex-1 max-w-sm">
                    <svg
                        class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-sf-tertiary pointer-events-none"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"
                        />
                    </svg>
                    <input
                        v-model="search"
                        type="search"
                        placeholder="Search events, venues..."
                        class="w-full pl-9 pr-4 py-2 font-body text-sm bg-sf-surface border border-sf-border-subtle rounded-lg text-sf-text placeholder:text-sf-tertiary focus:outline-none focus:border-sf-border transition-colors"
                    />
                </div>
                <button
                    type="button"
                    :class="[
                        'shrink-0 font-body text-sm px-4 py-2 rounded-lg border transition-colors',
                        upcoming
                            ? 'bg-sf-gold/10 border-sf-gold/30 text-sf-gold'
                            : 'bg-transparent border-sf-border-subtle text-sf-muted hover:text-sf-text hover:border-sf-border',
                    ]"
                    @click="upcoming = !upcoming"
                >
                    Upcoming
                </button>
            </div>

            <EventsEmptyState v-if="events.data.length === 0" />

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
                        <EventCardSkeleton v-for="i in 3" :key="i" />
                    </div>
                </template>
            </InfiniteScroll>
        </PageContainer>
    </HomeLayout>
</template>
