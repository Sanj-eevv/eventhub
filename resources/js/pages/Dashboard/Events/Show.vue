<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import { ArrowLeftIcon, ScanLine } from "lucide-vue-next";
import { computed, ref } from "vue";
import EventStatusBadge from "@/components/Dashboard/Events/EventStatusBadge.vue";
import ImageLightbox from "@/components/ImageLightbox.vue";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { usePermission } from "@/composables/usePermission";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import {
    formatCurrency,
    withTimezone,
    formatDate,
    formatTime,
} from "@/lib/utils";
import type { BreadcrumbItem } from "@/types";
import type { EventResource, MediaResource } from "@/types/event";
import { index as dashboardIndex } from "@/wayfinder/routes/dashboard";
import {
    checkIn as eventsCheckIn,
    edit as eventsEdit,
    index as eventsIndex,
    show as eventsShow,
} from "@/wayfinder/routes/dashboard/events";
import { show as orgsShow } from "@/wayfinder/routes/dashboard/organizations";
import { show as usersShow } from "@/wayfinder/routes/dashboard/users";

const canCheckIn = usePermission("checkIn")("manage");

const props = defineProps<{
    event: EventResource;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: dashboardIndex().url },
    { title: "Events", href: eventsIndex().url },
    {
        title: props.event.title,
        href: eventsShow({ event: props.event.uuid }).url,
    },
];

const galleryImages = computed<MediaResource[]>(
    () => props.event.media?.filter((item) => !item.is_cover) ?? [],
);

const lightboxOpen = ref(false);
const lightboxIndex = ref(0);

function openLightbox(index: number): void {
    lightboxIndex.value = index;
    lightboxOpen.value = true;
}
</script>

<template>
    <DashboardLayout :breadcrumbs="breadcrumbs">
        <Head :title="event.title" />

        <div class="space-y-6 p-6">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="sm" as-child>
                    <Link :href="eventsIndex().url">
                        <ArrowLeftIcon class="size-4" />
                        Back
                    </Link>
                </Button>
                <h1 class="flex-1 min-w-0 text-2xl font-bold truncate">
                    {{ event.title }}
                </h1>
                <div class="flex items-center gap-2 shrink-0">
                    <EventStatusBadge :status="event.status" />
                    <Button
                        v-if="canCheckIn"
                        variant="outline"
                        size="sm"
                        as-child
                    >
                        <Link :href="eventsCheckIn({ event: event.uuid }).url">
                            <ScanLine class="size-4" />
                            Check In
                        </Link>
                    </Button>
                    <Button variant="outline" size="sm" as-child>
                        <Link :href="eventsEdit({ event: event.uuid }).url"
                            >Edit</Link
                        >
                    </Button>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <Card>
                    <CardHeader><CardTitle>Details</CardTitle></CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm text-muted-foreground">Starts</p>
                            <p class="font-medium">
                                {{
                                    formatDate(event.starts_at, event.timezone)
                                }}
                                ·
                                {{
                                    withTimezone(formatTime)(
                                        event.starts_at,
                                        event.timezone,
                                    )
                                }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Ends</p>
                            <p class="font-medium">
                                {{ formatDate(event.ends_at, event.timezone) }}
                                ·
                                {{
                                    withTimezone(formatTime)(
                                        event.ends_at,
                                        event.timezone,
                                    )
                                }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Created</p>
                            <p class="font-medium">
                                {{ formatDate(event.created_at) }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <div class="space-y-6">
                    <Card v-if="event.organization">
                        <CardHeader
                            ><CardTitle>Organization</CardTitle></CardHeader
                        >
                        <CardContent>
                            <Link
                                :href="
                                    orgsShow({
                                        organization: event.organization.uuid,
                                    })
                                "
                                class="font-medium text-blue-600 hover:underline"
                            >
                                {{ event.organization.title }}
                            </Link>
                        </CardContent>
                    </Card>
                    <Card v-if="event.user">
                        <CardHeader
                            ><CardTitle>Created By</CardTitle></CardHeader
                        >
                        <CardContent>
                            <Link
                                :href="usersShow({ user: event.user.uuid })"
                                class="font-medium text-blue-600 hover:underline"
                            >
                                {{ event.user.name }}
                            </Link>
                        </CardContent>
                    </Card>
                </div>
            </div>
            <Card v-if="event.description">
                <CardHeader><CardTitle>About</CardTitle></CardHeader>
                <CardContent>
                    <p
                        class="text-sm text-muted-foreground leading-relaxed whitespace-pre-line"
                    >
                        {{ event.description }}
                    </p>
                </CardContent>
            </Card>

            <!-- Venue -->
            <Card v-if="event.venue_name || event.address">
                <CardHeader><CardTitle>Venue</CardTitle></CardHeader>
                <CardContent class="space-y-1">
                    <p v-if="event.venue_name" class="font-medium">
                        {{ event.venue_name }}
                    </p>
                    <p
                        v-if="event.address"
                        class="text-sm text-muted-foreground"
                    >
                        {{ event.address }}
                    </p>
                    <p v-if="event.zip" class="text-sm text-muted-foreground">
                        {{ event.zip }}
                    </p>
                    <a
                        v-if="event.map_url"
                        :href="event.map_url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 text-sm text-blue-600 hover:underline mt-2"
                    >
                        Open in Maps ↗
                    </a>
                </CardContent>
            </Card>

            <!-- Ticket types -->
            <Card v-if="event.ticket_types?.length">
                <CardHeader><CardTitle>Ticket Types</CardTitle></CardHeader>
                <CardContent>
                    <div class="divide-y">
                        <div
                            v-for="ticketType in event.ticket_types"
                            :key="ticketType.uuid"
                            class="py-4 first:pt-0 last:pb-0 flex items-start justify-between gap-4"
                        >
                            <div class="space-y-1">
                                <p class="font-medium">{{ ticketType.name }}</p>
                                <p
                                    v-if="ticketType.description"
                                    class="text-sm text-muted-foreground"
                                >
                                    {{ ticketType.description }}
                                </p>
                                <div
                                    class="flex flex-wrap gap-4 text-xs text-muted-foreground mt-1"
                                >
                                    <span
                                        >Capacity:
                                        {{ ticketType.capacity }}</span
                                    >
                                    <span v-if="ticketType.max_per_user"
                                        >Max per user:
                                        {{ ticketType.max_per_user }}</span
                                    >
                                    <span v-if="ticketType.sale_starts_at"
                                        >Sale starts:
                                        {{
                                            formatDate(
                                                ticketType.sale_starts_at,
                                            )
                                        }}</span
                                    >
                                    <span v-if="ticketType.sale_ends_at"
                                        >Sale ends:
                                        {{
                                            formatDate(ticketType.sale_ends_at)
                                        }}</span
                                    >
                                </div>
                            </div>
                            <p class="font-semibold text-lg shrink-0">
                                {{ formatCurrency(ticketType.price) }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card v-if="galleryImages.length">
                <CardHeader><CardTitle>Gallery</CardTitle></CardHeader>
                <CardContent>
                    <div
                        class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4"
                    >
                        <button
                            v-for="(image, index) in galleryImages"
                            :key="image.uuid"
                            type="button"
                            class="aspect-square overflow-hidden rounded-lg border bg-muted cursor-zoom-in group"
                            @click="openLightbox(index)"
                        >
                            <img
                                :src="image.url"
                                :alt="image.filename"
                                class="h-full w-full object-cover transition-transform duration-200 group-hover:scale-105"
                            />
                        </button>
                    </div>
                </CardContent>
            </Card>
        </div>

        <ImageLightbox
            v-model:open="lightboxOpen"
            :images="galleryImages"
            :initial-index="lightboxIndex"
        />
    </DashboardLayout>
</template>
