<script setup lang="ts">
import { router, useForm } from "@inertiajs/vue3";
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
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: dashboardIndex().url },
    { title: "Events", href: eventsIndex().url },
    { title: "Create Event", href: eventsCreate().url },
];

const form = useForm({
    organization_id: "",
    title: "",
    description: "",
    starts_at: "",
    ends_at: "",
    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
    location: {
        venue_name: "",
        address_line_1: "",
        address_line_2: "",
        city: "",
        state: "",
        zip: "",
        country: "",
        map_url: "",
    },
    tickets: [] as {
        _key: string;
        label: string;
        price: number | string;
        quantity: number | null;
        sale_starts_at: string | null;
        sale_ends_at: string | null;
    }[],
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        tickets: data.tickets.map(({ _key: _, ...rest }) => rest),
    })).post(store().url, {
        preserveScroll: true,
    });
};
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
                    :form="form"
                    :organizations="organizations"
                    @submit="submit"
                    @cancel="router.visit(eventsIndex().url)"
                />
            </div>
        </div>
    </DashboardLayout>
</template>
