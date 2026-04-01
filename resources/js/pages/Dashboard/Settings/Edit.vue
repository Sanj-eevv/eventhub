<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import type { BreadcrumbItem } from "@/types";
import { index as dashboardIndex } from "@/wayfinder/routes/dashboard";
import { update } from "@/wayfinder/routes/dashboard/settings";

type SettingsProps = {
    settings: {
        ticket_reservation_minutes: number;
        cancellation_cutoff_hours: number;
        refund_percentage: number;
    };
};

const props = defineProps<SettingsProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: dashboardIndex().url },
    { title: "Settings" },
];

const form = useForm({
    ticket_reservation_minutes: props.settings.ticket_reservation_minutes,
    cancellation_cutoff_hours: props.settings.cancellation_cutoff_hours,
    refund_percentage: props.settings.refund_percentage,
});

function submit(): void {
    form.put(update().url);
}
</script>

<template>
    <DashboardLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold tracking-tight">Settings</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Manage platform-wide configuration.
                </p>
            </div>

            <form class="max-w-2xl space-y-6" @submit.prevent="submit">
                <Card>
                    <CardHeader>
                        <CardTitle>Reservations</CardTitle>
                        <CardDescription>
                            Controls how long a ticket reservation is held
                            before it expires.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2">
                            <Label for="ticket_reservation_minutes">
                                Reservation Window (minutes)
                            </Label>
                            <Input
                                id="ticket_reservation_minutes"
                                v-model.number="
                                    form.ticket_reservation_minutes
                                "
                                type="number"
                                min="1"
                                max="60"
                                class="w-40"
                            />
                            <p
                                v-if="form.errors.ticket_reservation_minutes"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.ticket_reservation_minutes }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Cancellations</CardTitle>
                        <CardDescription>
                            Controls the cancellation window and refund amount
                            for paid orders.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div class="space-y-2">
                            <Label for="cancellation_cutoff_hours">
                                Cancellation Cutoff (hours before event)
                            </Label>
                            <Input
                                id="cancellation_cutoff_hours"
                                v-model.number="form.cancellation_cutoff_hours"
                                type="number"
                                min="0"
                                class="w-40"
                            />
                            <p
                                v-if="form.errors.cancellation_cutoff_hours"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.cancellation_cutoff_hours }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="refund_percentage">
                                Refund Percentage (%)
                            </Label>
                            <Input
                                id="refund_percentage"
                                v-model.number="form.refund_percentage"
                                type="number"
                                min="0"
                                max="100"
                                class="w-40"
                            />
                            <p
                                v-if="form.errors.refund_percentage"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.refund_percentage }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <div class="flex justify-end">
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? "Saving..." : "Save settings" }}
                    </Button>
                </div>
            </form>
        </div>
    </DashboardLayout>
</template>
