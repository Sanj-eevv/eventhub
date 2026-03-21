<script setup lang="ts">
import { Head, router } from "@inertiajs/vue3";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import type { BreadcrumbItem } from "@/types";
import { index as dashboardIndex } from "@/wayfinder/routes/dashboard";
import {
    index as eventsIndex,
    show as eventsShow,
} from "@/wayfinder/routes/dashboard/events";
import {
    create as ticketTypesCreate,
    destroy as ticketTypesDestroy,
    edit as ticketTypesEdit,
} from "@/wayfinder/routes/dashboard/events/ticket-types";

type TicketTypeRow = {
    uuid: string;
    name: string;
    price_formatted: string;
    capacity: number;
    is_active: boolean;
};

const props = defineProps<{
    event: { uuid: string; title: string; slug: string };
    ticketTypes: TicketTypeRow[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: dashboardIndex().url },
    { title: "Events", href: eventsIndex().url },
    { title: props.event.title, href: eventsShow({ event: props.event.uuid }).url },
    { title: "Ticket Types" },
];

function deleteTicketType(ticketTypeUuid: string): void {
    if (!confirm("Are you sure you want to delete this ticket type?")) {
        return;
    }
    router.delete(ticketTypesDestroy({ event: props.event.uuid, ticket_type: ticketTypeUuid }).url);
}
</script>

<template>
    <DashboardLayout :breadcrumbs="breadcrumbs">
        <Head :title="`Ticket Types — ${event.title}`" />
        <div class="space-y-4 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Ticket Types</h1>
                <Button @click="router.visit(ticketTypesCreate({ event: event.uuid }).url)">
                    Add Ticket Type
                </Button>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>{{ event.title }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="ticketTypes.length === 0"
                        class="text-center text-muted-foreground py-8"
                    >
                        No ticket types created yet.
                    </div>
                    <div v-else class="divide-y">
                        <div
                            v-for="ticketType in ticketTypes"
                            :key="ticketType.uuid"
                            class="flex items-center justify-between py-4"
                        >
                            <div class="space-y-0.5">
                                <p class="font-medium">{{ ticketType.name }}</p>
                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <span>{{ ticketType.price_formatted }}</span>
                                    <span>·</span>
                                    <span>Capacity: {{ ticketType.capacity }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <Badge :variant="ticketType.is_active ? 'default' : 'secondary'">
                                    {{ ticketType.is_active ? "Active" : "Inactive" }}
                                </Badge>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    @click="router.visit(ticketTypesEdit({ event: event.uuid, ticket_type: ticketType.uuid }).url)"
                                >
                                    Edit
                                </Button>
                                <Button
                                    variant="destructive"
                                    size="sm"
                                    @click="deleteTicketType(ticketType.uuid)"
                                >
                                    Delete
                                </Button>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </DashboardLayout>
</template>
