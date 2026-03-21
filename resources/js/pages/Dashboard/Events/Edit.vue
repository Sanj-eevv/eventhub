<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import { shallowRef } from "vue";
import EventForm from "@/components/Dashboard/Events/EventForm.vue";
import EventStatusBadge from "@/components/Dashboard/Events/EventStatusBadge.vue";
import { Button } from "@/components/ui/button";
import { Spinner } from "@/components/ui/spinner";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import type { BreadcrumbItem } from "@/types";
import type { Event } from "@/types/event";
import type { OrganizationPicker } from "@/types/organization";
import { index as dashboardIndex } from "@/wayfinder/routes/dashboard";
import {
    edit as eventsEdit,
    index as eventsIndex,
    publish,
    unpublish,
    update,
} from "@/wayfinder/routes/dashboard/events";

type StatusAction = "publish" | "unpublish";

const props = defineProps<{
    event: Event;
    organizations: OrganizationPicker[];
    timezones: string[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: dashboardIndex().url },
    { title: "Events", href: eventsIndex().url },
    { title: props.event.title, href: eventsEdit(props.event.uuid).url },
];

const initialValues = {
    organization_uuid: props.event.organization_uuid,
    title: props.event.title,
    description: props.event.description,
    starts_at: props.event.starts_at,
    ends_at: props.event.ends_at ?? "",
    timezone: props.event.timezone ?? Intl.DateTimeFormat().resolvedOptions().timeZone,
    location: {
        venue_name: props.event.location?.venue_name ?? "",
        address_line_1: props.event.location?.address_line_1 ?? "",
        address_line_2: props.event.location?.address_line_2 ?? "",
        zip: props.event.location?.zip ?? "",
        map_url: props.event.location?.map_url ?? "",
    },
    ticket_types: (props.event.ticket_types ?? []).map((ticketType) => ({
        _key: crypto.randomUUID(),
        uuid: ticketType.uuid,
        name: ticketType.name,
        price: ticketType.price.toFixed(2),
        capacity: String(ticketType.capacity),
        max_per_user: String(ticketType.max_per_user),
        sale_starts_at: ticketType.sale_starts_at ?? undefined,
        sale_ends_at: ticketType.sale_ends_at ?? undefined,
    })),
};

const isPublishing = shallowRef(false);

const routes: Record<StatusAction, ReturnType<typeof publish | typeof unpublish>> = {
    publish: publish(props.event.uuid),
    unpublish: unpublish(props.event.uuid),
};

const handleStatusChange = (action: StatusAction) => {
    isPublishing.value = true;
    router.post(routes[action].url, {}, {
        preserveScroll: true,
        onFinish: () => { isPublishing.value = false; },
    });
};
</script>

<template>
    <DashboardLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-[calc(100svh-4rem)] flex-col overflow-hidden group-has-data-[collapsible=icon]/sidebar-wrapper:h-[calc(100svh-3rem)]"
        >
            <div class="shrink-0 border-b px-6 py-5 lg:px-8">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h1 class="text-xl font-semibold tracking-tight">
                                Edit Event
                            </h1>
                            <EventStatusBadge :status="event.status" />
                        </div>
                        <p class="text-muted-foreground mt-0.5 text-sm">
                            {{ event.title }}
                        </p>
                    </div>

                    <div class="flex shrink-0 gap-2">
                        <Button
                            v-if="event.status.value === 'draft'"
                            type="button"
                            size="sm"
                            :disabled="isPublishing"
                            @click="handleStatusChange('publish')"
                        >
                            <Spinner v-if="isPublishing" />
                            Publish
                        </Button>
                        <Button
                            v-else-if="event.status.value === 'published'"
                            type="button"
                            variant="outline"
                            size="sm"
                            :disabled="isPublishing"
                            @click="handleStatusChange('unpublish')"
                        >
                            <Spinner v-if="isPublishing" />
                            Unpublish
                        </Button>
                    </div>
                </div>
            </div>

            <div class="flex min-h-0 flex-1 flex-col">
                <EventForm
                    :initial-values="initialValues"
                    :event-uuid="event.uuid"
                    :media-items="event.media"
                    :organizations="organizations"
                    :timezones="timezones"
                    submit-method="put"
                    :submit-url="update(event.uuid).url"
                    :is-editing="true"
                    @cancel="router.visit(eventsIndex().url)"
                />
            </div>
        </div>
    </DashboardLayout>
</template>
