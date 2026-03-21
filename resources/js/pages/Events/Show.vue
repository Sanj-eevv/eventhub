<script setup lang="ts">
import { computed, reactive } from "vue";
import { Head, useForm } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import HomeLayout from "@/layouts/HomeLayout.vue";
import type { PublicEvent, PublicTicketType } from "@/types/event";
import { reserve } from "@/wayfinder/routes/tickets";

const props = defineProps<{
    event: PublicEvent & { ticket_types: PublicTicketType[] };
}>();

const quantities = reactive<Record<string, number>>(
    Object.fromEntries(props.event.ticket_types.map((ticketType) => [ticketType.uuid, 0])),
);

const form = useForm(() => ({
    items: props.event.ticket_types
        .filter((ticketType) => quantities[ticketType.uuid] > 0)
        .map((ticketType) => ({
            ticket_type_id: ticketType.uuid,
            quantity: quantities[ticketType.uuid],
        })),
}));

const hasSelection = computed(() =>
    props.event.ticket_types.some((ticketType) => quantities[ticketType.uuid] > 0),
);

function submit(): void {
    form.post(reserve({ event: props.event.slug }).url);
}
</script>

<template>
    <HomeLayout>
        <Head :title="event.title" />
        <div class="mx-auto max-w-4xl px-4 py-10">
            <div class="mb-8">
                <h1 class="text-3xl font-bold">{{ event.title }}</h1>
                <p class="mt-2 text-muted-foreground">
                    {{ event.starts_at }}
                    <span v-if="event.ends_at"> — {{ event.ends_at }}</span>
                </p>
                <p v-if="event.location?.venue_name" class="mt-1 text-muted-foreground">
                    {{ event.location.venue_name
                    }}<span v-if="event.location.address_line_1">, {{ event.location.address_line_1 }}</span>
                </p>
            </div>

            <div class="grid gap-8 md:grid-cols-3">
                <div class="md:col-span-2 space-y-4">
                    <Card>
                        <CardHeader>
                            <CardTitle>About this event</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="whitespace-pre-line text-sm leading-relaxed">
                                {{ event.description }}
                            </p>
                        </CardContent>
                    </Card>
                </div>

                <div class="space-y-4">
                    <Card
                        v-for="ticketType in event.ticket_types"
                        :key="ticketType.uuid"
                    >
                        <CardHeader>
                            <CardTitle class="text-base">
                                {{ ticketType.name }}
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <p
                                v-if="ticketType.description"
                                class="text-sm text-muted-foreground"
                            >
                                {{ ticketType.description }}
                            </p>
                            <p class="text-lg font-semibold">
                                {{ ticketType.price_formatted }}
                            </p>
                            <div class="flex items-center gap-3">
                                <label
                                    :for="`qty-${ticketType.uuid}`"
                                    class="text-sm text-muted-foreground"
                                >
                                    Qty
                                </label>
                                <input
                                    :id="`qty-${ticketType.uuid}`"
                                    v-model.number="quantities[ticketType.uuid]"
                                    type="number"
                                    min="0"
                                    :max="ticketType.max_per_user"
                                    :disabled="!ticketType.is_active"
                                    class="w-20 rounded-md border border-input bg-background px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-ring disabled:opacity-50"
                                />
                            </div>
                        </CardContent>
                    </Card>

                    <Button
                        class="w-full"
                        :disabled="!hasSelection || form.processing"
                        @click="submit"
                    >
                        Reserve Tickets
                    </Button>

                    <p
                        v-if="form.errors.items"
                        class="text-sm text-destructive"
                    >
                        {{ form.errors.items }}
                    </p>
                </div>
            </div>
        </div>
    </HomeLayout>
</template>
