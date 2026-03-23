<script setup lang="ts">
import { Head, useForm } from "@inertiajs/vue3";
import { computed, reactive } from "vue";
import HomeLayout from "@/layouts/HomeLayout.vue";
import type { Event, PublicTicketType } from "@/types/event";
import { formatDate, formatTime } from "@/lib/utils";
import { reserve } from "@/wayfinder/routes/tickets";

const props = defineProps<{
    event: Event;
    ticketTypes: PublicTicketType[];
}>();

const quantities = reactive<Record<string, number>>(
    Object.fromEntries(
        props.ticketTypes.map((ticketType) => [ticketType.uuid, 0]),
    ),
);

const form = useForm(() => ({
    items: props.ticketTypes
        .filter((ticketType) => quantities[ticketType.uuid] > 0)
        .map((ticketType) => ({
            ticket_type_id: ticketType.uuid,
            quantity: quantities[ticketType.uuid],
        })),
}));

const hasSelection = computed(() =>
    props.ticketTypes.some((ticketType) => quantities[ticketType.uuid] > 0),
);

const orderTotal = computed(() =>
    props.ticketTypes.reduce((sum, ticketType) => {
        return sum + ticketType.price * (quantities[ticketType.uuid] ?? 0);
    }, 0),
);

const formatTotal = computed(() => {
    if (orderTotal.value === 0) return null;
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
    }).format(orderTotal.value / 100);
});

function submit(): void {
    form.post(reserve({ event: props.event.slug }).url);
}

const timezone = props.event.timezone;
</script>

<template>
    <HomeLayout>
        <Head :title="event.title" />

        <!-- Hero -->
        <div class="relative border-b border-sf-border-subtle">
            <!-- Cover image hero (when available) -->
            <div
                v-if="event.cover_image"
                class="relative h-[45vw] max-h-[560px] min-h-[260px] overflow-hidden bg-sf-surface"
            >
                <img
                    :src="event.cover_image.url"
                    :alt="event.title"
                    class="h-full w-full object-cover"
                />
                <div
                    class="absolute inset-0 bg-gradient-to-t from-sf-bg via-sf-bg/40 to-transparent"
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
                            {{ formatDate(event.starts_at, timezone) }} ·
                            {{ formatTime(event.starts_at, timezone) }}
                        </span>
                        <span v-if="event.ends_at" class="text-sf-tertiary"
                            >— {{ formatTime(event.ends_at, timezone) }}</span
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
            <!-- Fallback hero (no image) -->
            <div
                v-else
                class="bg-gradient-to-b from-sf-surface to-sf-bg transition-colors duration-200"
            >
                <div
                    class="absolute inset-0 bg-[radial-gradient(ellipse_70%_50%_at_50%_0%,color-mix(in_srgb,var(--sf-gold)_6%,transparent),transparent)]"
                />
                <div
                    class="relative mx-auto max-w-7xl px-5 sm:px-8 py-16 lg:py-24"
                >
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
                            {{ formatDate(event.starts_at, timezone) }} ·
                            {{ formatTime(event.starts_at, timezone) }}
                        </span>
                        <span v-if="event.ends_at" class="text-sf-tertiary"
                            >— {{ formatTime(event.ends_at, timezone) }}</span
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
        </div>

        <!-- Content -->
        <div class="mx-auto max-w-7xl px-5 sm:px-8 py-14">
            <div class="grid lg:grid-cols-3 gap-10 lg:gap-14">
                <!-- About + venue -->
                <div class="lg:col-span-2 space-y-10">
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <span class="h-px w-6 bg-sf-gold" />
                            <h2
                                class="font-display text-2xl font-medium text-sf-text"
                            >
                                About this Event
                            </h2>
                        </div>
                        <p
                            class="font-body font-light text-sf-muted leading-relaxed whitespace-pre-line text-base"
                        >
                            {{ event.description }}
                        </p>
                    </div>

                    <!-- Gallery strip -->
                    <div
                        v-if="event.media && event.media.length > 0"
                        class="border-t border-sf-border-subtle pt-10"
                    >
                        <div class="flex items-center gap-3 mb-5">
                            <span class="h-px w-6 bg-sf-gold" />
                            <h2
                                class="font-display text-2xl font-medium text-sf-text"
                            >
                                Gallery
                            </h2>
                        </div>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                            <div
                                v-for="image in event.media?.filter((m) => !m.is_cover)"
                                :key="image.uuid"
                                class="aspect-square overflow-hidden rounded-lg border border-sf-border-subtle bg-sf-surface"
                            >
                                <img
                                    :src="image.url"
                                    :alt="image.filename"
                                    class="h-full w-full object-cover transition-transform duration-300 hover:scale-105"
                                />
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="event.venue_name || event.address"
                        class="border-t border-sf-border-subtle pt-10"
                    >
                        <div class="flex items-center gap-3 mb-6">
                            <span class="h-px w-6 bg-sf-gold" />
                            <h2
                                class="font-display text-2xl font-medium text-sf-text"
                            >
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
                            <p
                                v-if="event.zip"
                                class="font-body text-sm text-sf-muted"
                            >
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
                </div>

                <!-- Ticket widget (sticky) -->
                <div class="lg:col-span-1">
                    <div class="sticky top-24 space-y-4">
                        <div
                            v-for="ticketType in ticketTypes"
                            :key="ticketType.uuid"
                            :class="[
                                'bg-sf-surface border rounded-xl p-5 transition-all duration-200',
                                !ticketType.is_active
                                    ? 'border-sf-border-subtle opacity-60'
                                    : quantities[ticketType.uuid] > 0
                                      ? 'border-sf-gold/40 bg-sf-gold/5'
                                      : 'border-sf-border-subtle hover:border-sf-border',
                            ]"
                        >
                            <div
                                class="flex items-start justify-between gap-4 mb-4"
                            >
                                <div class="flex-1">
                                    <p
                                        class="font-display text-base font-medium text-sf-text"
                                    >
                                        {{ ticketType.name }}
                                    </p>
                                    <p
                                        v-if="ticketType.description"
                                        class="font-body text-xs text-sf-muted mt-1 leading-relaxed"
                                    >
                                        {{ ticketType.description }}
                                    </p>
                                    <p
                                        v-if="!ticketType.is_active"
                                        class="font-body text-xs text-sf-ember mt-1"
                                    >
                                        Not available
                                    </p>
                                </div>
                                <span
                                    class="font-display text-xl font-medium text-sf-text shrink-0"
                                    >${{ ticketType.price.toFixed(2) }}</span
                                >
                            </div>

                            <div class="flex items-center gap-3">
                                <button
                                    type="button"
                                    class="h-8 w-8 flex items-center justify-center rounded border border-sf-border text-sf-muted hover:border-sf-gold hover:text-sf-gold transition-all disabled:opacity-30 disabled:pointer-events-none text-lg leading-none"
                                    :disabled="
                                        !ticketType.is_active ||
                                        quantities[ticketType.uuid] === 0
                                    "
                                    @click="
                                        quantities[ticketType.uuid] = Math.max(
                                            0,
                                            quantities[ticketType.uuid] - 1,
                                        )
                                    "
                                >
                                    −
                                </button>
                                <span
                                    class="font-code text-sm text-sf-text w-6 text-center tabular-nums"
                                    >{{ quantities[ticketType.uuid] }}</span
                                >
                                <button
                                    type="button"
                                    class="h-8 w-8 flex items-center justify-center rounded border border-sf-border text-sf-muted hover:border-sf-gold hover:text-sf-gold transition-all disabled:opacity-30 disabled:pointer-events-none text-lg leading-none"
                                    :disabled="
                                        !ticketType.is_active ||
                                        (ticketType.max_per_user !== null &&
                                            quantities[ticketType.uuid] >=
                                                ticketType.max_per_user)
                                    "
                                    @click="
                                        quantities[ticketType.uuid] =
                                            ticketType.max_per_user !== null
                                                ? Math.min(
                                                      ticketType.max_per_user,
                                                      quantities[
                                                          ticketType.uuid
                                                      ] + 1,
                                                  )
                                                : quantities[ticketType.uuid] +
                                                  1
                                    "
                                >
                                    +
                                </button>
                                <span
                                    v-if="ticketType.max_per_user !== null"
                                    class="font-body text-xs text-sf-tertiary ml-auto"
                                    >max {{ ticketType.max_per_user }}</span
                                >
                            </div>
                        </div>

                        <div
                            v-if="ticketTypes.length === 0"
                            class="bg-sf-surface border border-sf-border-subtle rounded-xl p-6 text-center"
                        >
                            <p class="font-body text-sm text-sf-tertiary">
                                No tickets available yet.
                            </p>
                        </div>

                        <div
                            v-if="ticketTypes.length > 0"
                            class="pt-1 space-y-3"
                        >
                            <div
                                v-if="formatTotal"
                                class="flex justify-between items-center px-1"
                            >
                                <span class="font-body text-sm text-sf-muted"
                                    >Total</span
                                >
                                <span
                                    class="font-display text-xl font-medium text-sf-text"
                                    >{{ formatTotal }}</span
                                >
                            </div>
                            <button
                                type="button"
                                :disabled="!hasSelection || form.processing"
                                class="w-full py-3.5 rounded bg-sf-ember text-white font-body text-sm tracking-wide hover:bg-sf-ember-hover active:scale-95 transition-all duration-200 disabled:opacity-40 disabled:pointer-events-none"
                                @click="submit"
                            >
                                <span v-if="form.processing">Reserving…</span>
                                <span v-else-if="hasSelection"
                                    >Reserve Tickets</span
                                >
                                <span v-else>Select tickets above</span>
                            </button>
                            <p
                                v-if="form.errors.items"
                                class="font-body text-xs text-sf-ember text-center"
                            >
                                {{ form.errors.items }}
                            </p>
                            <p
                                class="font-body text-xs text-sf-tertiary text-center"
                            >
                                Free reservations · Secure Stripe checkout
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </HomeLayout>
</template>
