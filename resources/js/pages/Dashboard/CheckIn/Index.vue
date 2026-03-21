<script setup lang="ts">
import { ref } from "vue";
import { Head } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import type { BreadcrumbItem } from "@/types";
import { index as dashboardIndex } from "@/wayfinder/routes/dashboard";
import {
    index as eventsIndex,
    show as eventsShow,
} from "@/wayfinder/routes/dashboard/events";
import { scan } from "@/wayfinder/routes/dashboard/events/check-in";

const props = defineProps<{
    event: { uuid: string; title: string; slug: string };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: dashboardIndex().url },
    { title: "Events", href: eventsIndex().url },
    { title: props.event.title, href: eventsShow({ event: props.event.uuid }).url },
    { title: "Check-In" },
];

type ScanResult = {
    success: boolean;
    message: string;
    attendee_name?: string;
    ticket_type?: string;
};

const bookingReference = ref("");
const isLoading = ref(false);
const scanResult = ref<ScanResult | null>(null);

async function submitScan(): Promise<void> {
    if (!bookingReference.value.trim() || isLoading.value) {
        return;
    }

    isLoading.value = true;
    scanResult.value = null;

    try {
        const response = await fetch(scan({ event: props.event.uuid }).url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-XSRF-TOKEN": decodeURIComponent(
                    document.cookie
                        .split("; ")
                        .find((row) => row.startsWith("XSRF-TOKEN="))
                        ?.split("=")[1] ?? "",
                ),
            },
            body: JSON.stringify({ booking_reference: bookingReference.value }),
        });

        const data = await response.json();
        scanResult.value = data;

        if (data.success) {
            bookingReference.value = "";
        }
    } catch {
        scanResult.value = { success: false, message: "Network error. Please try again." };
    } finally {
        isLoading.value = false;
    }
}
</script>

<template>
    <DashboardLayout :breadcrumbs="breadcrumbs">
        <Head :title="`Check-In — ${event.title}`" />
        <div class="p-6">
            <h1 class="mb-2 text-2xl font-bold">Check-In</h1>
            <p class="mb-8 text-muted-foreground">{{ event.title }}</p>

            <div class="mx-auto max-w-md space-y-6">
                <div class="space-y-3">
                    <Label
                        for="booking_reference"
                        class="text-base font-medium"
                    >
                        Booking Reference
                    </Label>
                    <Input
                        id="booking_reference"
                        v-model="bookingReference"
                        class="h-14 text-lg tracking-widest uppercase"
                        placeholder="e.g. ABC-123456"
                        autocomplete="off"
                        autocorrect="off"
                        autocapitalize="characters"
                        @keydown.enter="submitScan"
                    />
                    <Button
                        class="w-full h-12 text-base"
                        :disabled="!bookingReference.trim() || isLoading"
                        @click="submitScan"
                    >
                        {{ isLoading ? "Checking in..." : "Check In" }}
                    </Button>
                </div>

                <div
                    v-if="scanResult"
                    class="rounded-lg p-5"
                    :class="scanResult.success ? 'bg-green-50 border border-green-200' : 'bg-destructive/10 border border-destructive/20'"
                >
                    <p
                        class="font-semibold text-lg"
                        :class="scanResult.success ? 'text-green-800' : 'text-destructive'"
                    >
                        {{ scanResult.success ? "Check-In Successful" : "Check-In Failed" }}
                    </p>
                    <p
                        class="mt-1 text-sm"
                        :class="scanResult.success ? 'text-green-700' : 'text-destructive/80'"
                    >
                        {{ scanResult.message }}
                    </p>
                    <div
                        v-if="scanResult.success && scanResult.attendee_name"
                        class="mt-3 space-y-1"
                    >
                        <p class="text-sm font-medium text-green-800">
                            {{ scanResult.attendee_name }}
                        </p>
                        <p
                            v-if="scanResult.ticket_type"
                            class="text-sm text-green-700"
                        >
                            {{ scanResult.ticket_type }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
