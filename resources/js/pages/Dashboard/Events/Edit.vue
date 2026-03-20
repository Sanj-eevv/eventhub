<script setup lang="ts">
import { router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
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

const props = defineProps<{
    event: Event;
    organizations: OrganizationPicker[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: dashboardIndex().url },
    { title: "Events", href: eventsIndex().url },
    { title: props.event.title, href: eventsEdit(props.event.uuid).url },
];

const formatForInput = (iso: string | null | undefined) =>
    iso?.substring(0, 16) ?? "";

const form = useForm({
    organization_id: props.event.organization_id,
    title: props.event.title,
    description: props.event.description,
    starts_at: formatForInput(props.event.starts_at),
    ends_at: formatForInput(props.event.ends_at),
    timezone:
        props.event.timezone ??
        Intl.DateTimeFormat().resolvedOptions().timeZone,
    location: {
        venue_name: props.event.location?.venue_name ?? "",
        address_line_1: props.event.location?.address_line_1 ?? "",
        address_line_2: props.event.location?.address_line_2 ?? "",
        city: props.event.location?.city ?? "",
        state: props.event.location?.state ?? "",
        zip: props.event.location?.zip ?? "",
        country: props.event.location?.country ?? "",
        map_url: props.event.location?.map_url ?? "",
    },
    tickets: (props.event.tickets ?? []).map((t) => ({
        ...t,
        _key: crypto.randomUUID(),
    })) as {
        _key: string;
        label: string;
        price: number | string;
        quantity: number | null;
        sale_starts_at: string | null;
        sale_ends_at: string | null;
    }[],
});

const isPublishing = ref(false);

const submit = () => {
    form.transform((data) => ({
        ...data,
        tickets: data.tickets.map(({ _key: _, ...rest }) => rest),
    })).put(update(props.event.uuid).url, {
        preserveScroll: true,
    });
};

const handlePublish = () => {
    isPublishing.value = true;
    router.post(
        publish(props.event.uuid).url,
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                isPublishing.value = false;
            },
        },
    );
};

const handleUnpublish = () => {
    isPublishing.value = true;
    router.post(
        unpublish(props.event.uuid).url,
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
                            v-if="event.status === 'draft'"
                            type="button"
                            size="sm"
                            :disabled="isPublishing"
                            @click="handlePublish"
                        >
                            <Spinner v-if="isPublishing" />
                            Publish
                        </Button>
                        <Button
                            v-else-if="event.status === 'published'"
                            type="button"
                            variant="outline"
                            size="sm"
                            :disabled="isPublishing"
                            @click="handleUnpublish"
                        >
                            <Spinner v-if="isPublishing" />
                            Unpublish
                        </Button>
                    </div>
                </div>
            </div>

            <div class="flex min-h-0 flex-1 flex-col">
                <EventForm
                    :form="form"
                    :organizations="organizations"
                    :is-editing="true"
                    @submit="submit"
                    @cancel="router.visit(eventsIndex().url)"
                />
            </div>
        </div>
    </DashboardLayout>
</template>
