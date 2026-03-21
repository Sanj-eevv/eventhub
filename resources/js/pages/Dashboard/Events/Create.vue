<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import EventForm from "@/components/Dashboard/Events/EventForm.vue";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import type { BreadcrumbItem } from "@/types";
import type { OrganizationPicker } from "@/types/organization";
import { index as dashboardIndex } from "@/wayfinder/routes/dashboard";
import {
    create as eventsCreate,
    index as eventsIndex,
    store,
} from "@/wayfinder/routes/dashboard/events";

defineProps<{
    organizations: OrganizationPicker[];
    timezones: string[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: dashboardIndex().url },
    { title: "Events", href: eventsIndex().url },
    { title: "Create Event", href: eventsCreate().url },
];
</script>

<template>
    <DashboardLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-[calc(100svh-4rem)] flex-col overflow-hidden group-has-data-[collapsible=icon]/sidebar-wrapper:h-[calc(100svh-3rem)]">
            <div class="shrink-0 border-b px-6 py-5 lg:px-8">
                <h1 class="text-xl font-semibold tracking-tight">Create Event</h1>
                <p class="text-muted-foreground mt-0.5 text-sm">
                    Fill in the details below to publish a new event.
                </p>
            </div>

            <div class="flex min-h-0 flex-1 flex-col">
                <EventForm
                    :organizations="organizations"
                    :timezones="timezones"
                    submit-method="post"
                    :submit-url="store().url"
                    @cancel="router.visit(eventsIndex().url)"
                />
            </div>
        </div>
    </DashboardLayout>
</template>
