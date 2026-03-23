<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import HomeLayout from "@/layouts/HomeLayout.vue";
import EventAbout from "@/components/Events/EventAbout.vue";
import EventGallery from "@/components/Events/EventGallery.vue";
import EventHero from "@/components/Events/EventHero.vue";
import EventVenue from "@/components/Events/EventVenue.vue";
import TicketSelector from "@/components/Events/TicketSelector.vue";
import type { EventResource, TicketTypeResource } from "@/types/event";

defineProps<{
    event: EventResource;
    ticketTypes: TicketTypeResource[];
}>();
</script>

<template>
    <HomeLayout>
        <Head :title="event.title" />

        <EventHero :event="event" />

        <div class="mx-auto max-w-7xl px-5 sm:px-8 py-14">
            <div class="grid lg:grid-cols-3 gap-10 lg:gap-14">
                <div class="lg:col-span-2 space-y-10">
                    <EventAbout :description="event.description ?? ''" />
                    <EventGallery :media="event.media" />
                    <EventVenue :event="event" />
                </div>
                <div class="lg:col-span-1">
                    <TicketSelector
                        :ticket-types="ticketTypes"
                        :event-slug="event.slug"
                    />
                </div>
            </div>
        </div>
    </HomeLayout>
</template>
