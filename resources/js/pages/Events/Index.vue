<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import HomeLayout from "@/layouts/HomeLayout.vue";
import type { PaginatedResponse } from "@/types";
import type { PublicEvent } from "@/types/event";
import { show } from "@/wayfinder/routes/events";

defineProps<{
    events: PaginatedResponse<PublicEvent>;
}>();
</script>

<template>
    <HomeLayout>
        <Head title="Browse Events" />
        <div class="mx-auto max-w-6xl px-4 py-10">
            <h1 class="mb-8 text-3xl font-bold">Browse Events</h1>
            <div
                v-if="events.data.length === 0"
                class="text-center text-muted-foreground py-16"
            >
                No events available at the moment.
            </div>
            <div
                v-else
                class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
            >
                <Card
                    v-for="event in events.data"
                    :key="event.uuid"
                    class="flex flex-col"
                >
                    <CardHeader>
                        <CardTitle class="text-lg">{{ event.title }}</CardTitle>
                    </CardHeader>
                    <CardContent class="flex flex-1 flex-col gap-3">
                        <p class="text-sm text-muted-foreground line-clamp-3">
                            {{ event.description }}
                        </p>
                        <p class="text-sm text-muted-foreground">
                            {{ event.starts_at }}
                        </p>
                        <div class="mt-auto">
                            <Link
                                :href="show({ event: event.slug }).url"
                                class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90 transition-colors"
                            >
                                View Event
                            </Link>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </HomeLayout>
</template>
