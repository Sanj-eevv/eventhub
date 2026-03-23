<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import { onMounted, shallowRef } from "vue";
import EventForm from "@/components/Dashboard/Events/EventForm.vue";
import EventStatusBadge from "@/components/Dashboard/Events/EventStatusBadge.vue";
import { Button } from "@/components/ui/button";
import { Spinner } from "@/components/ui/spinner";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import type { BreadcrumbItem } from "@/types";
import type { EventResource } from "@/types/event";
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
    event: EventResource;
    organizations: OrganizationPicker[];
    timezones: string[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: dashboardIndex().url },
    { title: "Events", href: eventsIndex().url },
    { title: props.event.title, href: eventsEdit(props.event.uuid).url },
];

const isPublishing = shallowRef(false);
const eventForm = shallowRef<InstanceType<typeof EventForm> | null>(null);

onMounted(() => {
    const focus = new URLSearchParams(window.location.search).get("focus");
    if (focus) {
        eventForm.value?.scrollToSection(
            focus as "details" | "location" | "tickets" | "media",
        );
    }
});

const routes: Record<
    StatusAction,
    ReturnType<typeof publish | typeof unpublish>
> = {
    publish: publish(props.event.uuid),
    unpublish: unpublish(props.event.uuid),
};

const handleStatusChange = (action: StatusAction) => {
    isPublishing.value = true;
    router.post(
        routes[action].url,
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                isPublishing.value = false;
            },
        },
    );
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
                    ref="eventForm"
                    :initial-values="props.event"
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
