<script setup lang="ts">
import { Head, Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import ActiveReservationPanel from "@/components/Events/ActiveReservationPanel.vue";
import EventAbout from "@/components/Events/EventAbout.vue";
import EventGallery from "@/components/Events/EventGallery.vue";
import EventHero from "@/components/Events/EventHero.vue";
import EventVenue from "@/components/Events/EventVenue.vue";
import TicketSelector from "@/components/Events/TicketSelector.vue";
import PageContainer from "@/components/PageContainer.vue";
import HomeLayout from "@/layouts/HomeLayout.vue";
import type { ActiveOrderResource, EventResource } from "@/types/event";
import { login as loginCreate } from "@/wayfinder/routes/auth";

defineProps<{
    event: EventResource;
    activeOrder: ActiveOrderResource | null;
}>();

const isAuthenticated = computed(() => !!usePage().props.auth.user);
</script>

<template>
    <HomeLayout>
        <Head :title="event.title" />
        <PageContainer>
            <EventHero :event="event" />

            <div class="grid lg:grid-cols-3 gap-10 lg:gap-14 pt-10">
                <div class="lg:col-span-2 space-y-10">
                    <EventAbout :description="event.description ?? ''" />
                    <EventGallery :media="event.media" />
                    <EventVenue :event="event" />
                </div>
                <div class="lg:col-span-1">
                    <ActiveReservationPanel
                        v-if="activeOrder"
                        :active-order="activeOrder"
                    />
                    <TicketSelector
                        v-else-if="isAuthenticated"
                        :ticket-types="event.ticket_types"
                        :event-slug="event.slug"
                        :event-timezone="event.timezone"
                    />
                    <div
                        v-else
                        class="sticky top-24 bg-sf-surface border border-sf-border-subtle rounded-xl p-8 text-center space-y-5"
                    >
                        <div
                            class="inline-flex h-14 w-14 items-center justify-center rounded-full border border-sf-border"
                        >
                            <svg
                                class="h-6 w-6 text-sf-gold"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"
                                />
                            </svg>
                        </div>
                        <div class="space-y-2">
                            <p
                                class="font-display text-lg font-medium text-sf-text"
                            >
                                Reserve your spot
                            </p>
                            <p class="font-body text-sm text-sf-muted">
                                Sign in to select tickets and secure your place
                                at this event.
                            </p>
                        </div>
                        <div class="space-y-3 pt-1">
                            <Link
                                :href="loginCreate()"
                                class="block w-full py-3 rounded bg-sf-ember text-white font-body text-sm tracking-wide hover:bg-sf-ember-hover transition-colors duration-200 text-center"
                            >
                                Sign in
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </PageContainer>
    </HomeLayout>
</template>
