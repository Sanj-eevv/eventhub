<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import { useForm } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Checkbox } from "@/components/ui/checkbox";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import type { BreadcrumbItem } from "@/types";
import { index as dashboardIndex } from "@/wayfinder/routes/dashboard";
import {
    index as eventsIndex,
    show as eventsShow,
} from "@/wayfinder/routes/dashboard/events";
import {
    index as ticketTypesIndex,
    store as ticketTypesStore,
} from "@/wayfinder/routes/dashboard/events/ticket-types";

const props = defineProps<{
    event: { uuid: string; title: string; slug: string };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: dashboardIndex().url },
    { title: "Events", href: eventsIndex().url },
    { title: props.event.title, href: eventsShow({ event: props.event.uuid }).url },
    {
        title: "Ticket Types",
        href: ticketTypesIndex({ event: props.event.uuid }).url,
    },
    { title: "New Ticket Type" },
];

const form = useForm({
    name: "",
    description: "",
    price: "",
    capacity: "",
    max_per_user: "",
    is_active: true,
    sale_starts_at: "",
    sale_ends_at: "",
});

function submit(): void {
    form.post(ticketTypesStore({ event: props.event.uuid }).url);
}
</script>

<template>
    <DashboardLayout :breadcrumbs="breadcrumbs">
        <Head title="New Ticket Type" />
        <div class="p-6">
            <h1 class="mb-6 text-2xl font-bold">New Ticket Type</h1>

            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle>{{ event.title }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <form class="space-y-5" @submit.prevent="submit">
                        <div class="space-y-2">
                            <Label for="name">Name</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                placeholder="e.g. General Admission"
                            />
                            <p
                                v-if="form.errors.name"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Description</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                placeholder="Optional description"
                                rows="3"
                            />
                            <p
                                v-if="form.errors.description"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="price">Price (in dollars, e.g. 25.00)</Label>
                            <Input
                                id="price"
                                v-model="form.price"
                                type="number"
                                min="0"
                                step="0.01"
                                placeholder="25.00"
                            />
                            <p
                                v-if="form.errors.price"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.price }}
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="capacity">Capacity</Label>
                                <Input
                                    id="capacity"
                                    v-model="form.capacity"
                                    type="number"
                                    min="1"
                                    placeholder="100"
                                />
                                <p
                                    v-if="form.errors.capacity"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.capacity }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="max_per_user">Max per user</Label>
                                <Input
                                    id="max_per_user"
                                    v-model="form.max_per_user"
                                    type="number"
                                    min="1"
                                    placeholder="4"
                                />
                                <p
                                    v-if="form.errors.max_per_user"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.max_per_user }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="sale_starts_at">Sale starts at</Label>
                                <Input
                                    id="sale_starts_at"
                                    v-model="form.sale_starts_at"
                                    type="datetime-local"
                                />
                                <p
                                    v-if="form.errors.sale_starts_at"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.sale_starts_at }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="sale_ends_at">Sale ends at</Label>
                                <Input
                                    id="sale_ends_at"
                                    v-model="form.sale_ends_at"
                                    type="datetime-local"
                                />
                                <p
                                    v-if="form.errors.sale_ends_at"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.sale_ends_at }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <Checkbox
                                id="is_active"
                                :checked="form.is_active"
                                @update:checked="(value) => (form.is_active = value)"
                            />
                            <Label for="is_active">Active (visible to buyers)</Label>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                            >
                                Create Ticket Type
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </DashboardLayout>
</template>
