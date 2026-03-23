<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import { ArrowLeftIcon } from "lucide-vue-next";
import EventStatusBadge from "@/components/Dashboard/Events/EventStatusBadge.vue";
import { formatDate, formatTime } from "@/lib/utils";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import type { BreadcrumbItem } from "@/types";
import type { Event } from "@/types/event";
import { index as dashboardIndex } from "@/wayfinder/routes/dashboard";
import {
    edit as eventsEdit,
    index as eventsIndex,
    show as eventsShow,
} from "@/wayfinder/routes/dashboard/events";
import { show as orgsShow } from "@/wayfinder/routes/dashboard/organizations";
import { show as usersShow } from "@/wayfinder/routes/dashboard/users";

const props = defineProps<{
    event: Event;
}>();

const goBack = () => window.history.back();

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: dashboardIndex().url },
    { title: "Events", href: eventsIndex().url },
    {
        title: props.event.title,
        href: eventsShow({ event: props.event.uuid }).url,
    },
];
</script>

<template>
    <DashboardLayout :breadcrumbs="breadcrumbs">
        <Head :title="event.title" />
        <div class="space-y-6 p-6">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="sm" @click="goBack">
                    <ArrowLeftIcon class="size-4" />
                    Back
                </Button>
                <h1 class="text-2xl font-bold">{{ event.title }}</h1>
                <Button variant="outline" size="sm" as-child class="ml-auto">
                    <Link :href="eventsEdit({ event: event.uuid }).url">
                        Edit
                    </Link>
                </Button>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Details</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div v-if="event.description">
                            <p class="text-sm text-muted-foreground">
                                Description
                            </p>
                            <p class="font-medium">{{ event.description }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">
                                Starts At
                            </p>
                            <p class="font-medium">{{ formatDate(event.starts_at, event.timezone) }} · {{ formatTime(event.starts_at, event.timezone) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Ends At</p>
                            <p class="font-medium">{{ event.ends_at ? `${formatDate(event.ends_at, event.timezone)} · ${formatTime(event.ends_at, event.timezone)}` : '—' }}</p>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Status & Dates</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm text-muted-foreground">Status</p>
                            <EventStatusBadge :status="event.status" />
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Created</p>
                            <p class="font-medium">{{ formatDate(event.created_at) }}</p>
                        </div>
                    </CardContent>
                </Card>

                <Card v-if="event.organization">
                    <CardHeader>
                        <CardTitle>Organization</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm text-muted-foreground">Name</p>
                            <Link
                                :href="orgsShow({ organization: event.organization.uuid })"
                                class="font-medium text-blue-600 hover:underline"
                            >
                                {{ event.organization.title }}
                            </Link>
                        </div>
                    </CardContent>
                </Card>

                <Card v-if="event.user">
                    <CardHeader>
                        <CardTitle>Created By</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm text-muted-foreground">Name</p>
                            <Link
                                :href="usersShow({ user: event.user.uuid })"
                                class="font-medium text-blue-600 hover:underline"
                            >
                                {{ event.user.name }}
                            </Link>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </DashboardLayout>
</template>
