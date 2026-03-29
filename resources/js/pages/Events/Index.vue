<script setup lang="ts">
import { Head, InfiniteScroll } from "@inertiajs/vue3";
import EventCard from "@/components/Home/EventCard.vue";
import EventCardSkeleton from "@/components/Home/EventCardSkeleton.vue";
import EventsEmptyState from "@/components/Home/EventsEmptyState.vue";
import PageContainer from "@/components/PageContainer.vue";
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

        <PageContainer>
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
