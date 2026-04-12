<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import {
    CheckCircle2,
    QrCode,
    ScanLine,
    UserCheck,
    XCircle,
} from "lucide-vue-next";
import { onMounted, onUnmounted, ref, useTemplateRef } from "vue";
import { toast } from "vue-sonner";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { useCheckIn } from "@/composables/events/useCheckIn";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import type { BreadcrumbItem } from "@/types";
import type { EventResource } from "@/types/event";
import { index as dashboardIndex } from "@/wayfinder/routes/dashboard";
import {
    index as eventsIndex,
    show as eventsShow,
} from "@/wayfinder/routes/dashboard/events";

type CheckInFeedEntry = {
    attendee: string;
    ticketType: string;
    checkedInAt: string;
};

type TicketCheckedInPayload = {
    ticket_uuid: string;
    attendee: string;
    ticket_type: string;
    checked_in_at: string;
    total_checked_in: number;
    total_tickets: number;
};

type DuplicateScanPayload = {
    ticket_uuid: string;
    attendee: string;
    ticket_type: string;
};

const props = defineProps<{
    event: EventResource;
    initialCheckedIn: number;
    totalTickets: number;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: dashboardIndex().url },
    { title: "Events", href: eventsIndex().url },
    {
        title: props.event.title,
        href: eventsShow({ event: props.event.uuid }).url,
    },
    { title: "Check-In" },
];

const videoRef = useTemplateRef<HTMLVideoElement>("videoRef");

const {
    bookingReference,
    isLoading,
    scanResult,
    isCameraActive,
    cameraError,
    submitScan,
    startCamera,
    stopCamera,
} = useCheckIn(props.event.uuid);

const checkedInCount = ref(props.initialCheckedIn);
const totalTicketsCount = ref(props.totalTickets);
const liveFeed = ref<CheckInFeedEntry[]>([]);

function toggleCamera(): void {
    if (isCameraActive.value) {
        stopCamera();
    } else {
        startCamera(videoRef);
    }
}

onMounted(() => {
    window.Echo.private(`checkin.${props.event.uuid}`)
        .listen(".ticket.checked-in", (data: TicketCheckedInPayload) => {
            checkedInCount.value = data.total_checked_in;
            totalTicketsCount.value = data.total_tickets;
            liveFeed.value.unshift({
                attendee: data.attendee,
                ticketType: data.ticket_type,
                checkedInAt: data.checked_in_at,
            });
        })
        .listen(".ticket.duplicate-scan", (data: DuplicateScanPayload) => {
            toast.warning(
                `Duplicate scan: ${data.attendee} (${data.ticket_type}) already checked in.`,
            );
        });
});

onUnmounted(() => {
    window.Echo.leave(`checkin.${props.event.uuid}`);
});
</script>

<template>
    <DashboardLayout :breadcrumbs="breadcrumbs">
        <Head :title="`Check-In — ${event.title}`" />

        <div class="flex flex-col gap-6 p-6 h-full">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        Check-In
                    </h1>
                    <p class="text-sm text-muted-foreground mt-1">
                        {{ event.title }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold tabular-nums">
                        {{ checkedInCount
                        }}<span
                            class="text-muted-foreground font-normal text-xl"
                        >
                            / {{ totalTicketsCount }}</span
                        >
                    </p>
                    <p
                        class="text-xs text-muted-foreground uppercase tracking-wide mt-0.5"
                    >
                        Checked In
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 flex-1">
                <div class="flex flex-col gap-6">
                    <div class="rounded-xl border bg-card p-6 space-y-4">
                        <div class="flex items-center gap-2">
                            <QrCode class="size-4 text-muted-foreground" />
                            <span class="text-sm font-medium">QR Scanner</span>
                        </div>

                        <Button
                            :variant="
                                isCameraActive ? 'destructive' : 'outline'
                            "
                            class="w-full h-11"
                            @click="toggleCamera"
                        >
                            <ScanLine class="size-4 mr-2" />
                            {{
                                isCameraActive ? "Stop Camera" : "Start Camera"
                            }}
                        </Button>

                        <div
                            v-if="isCameraActive"
                            class="overflow-hidden rounded-lg bg-black aspect-video w-full"
                        >
                            <video
                                ref="videoRef"
                                class="w-full h-full object-cover"
                            />
                        </div>

                        <div
                            v-else-if="cameraError"
                            class="rounded-lg border-2 border-dashed border-destructive/30 bg-destructive/5 aspect-video flex items-center justify-center px-6"
                        >
                            <div class="text-center space-y-2">
                                <XCircle
                                    class="size-10 text-destructive/50 mx-auto"
                                />
                                <p class="text-sm text-destructive/80">
                                    {{ cameraError }}
                                </p>
                            </div>
                        </div>

                        <div
                            v-else
                            class="rounded-lg border-2 border-dashed border-muted aspect-video flex items-center justify-center"
                        >
                            <div class="text-center space-y-2">
                                <QrCode
                                    class="size-10 text-muted-foreground/40 mx-auto"
                                />
                                <p class="text-xs text-muted-foreground">
                                    Camera inactive
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card p-6 space-y-4">
                        <div class="flex items-center gap-2">
                            <UserCheck class="size-4 text-muted-foreground" />
                            <span class="text-sm font-medium"
                                >Manual Entry</span
                            >
                        </div>

                        <div class="space-y-2">
                            <Label
                                for="booking_reference"
                                class="text-xs text-muted-foreground uppercase tracking-wide"
                            >
                                Booking Reference
                            </Label>
                            <Input
                                id="booking_reference"
                                v-model="bookingReference"
                                class="h-12 text-base font-mono tracking-widest uppercase"
                                placeholder="EVT-123456"
                                autocomplete="off"
                                autocorrect="off"
                                autocapitalize="characters"
                                @keydown.enter="submitScan"
                            />
                        </div>

                        <Button
                            class="w-full h-11"
                            :disabled="!bookingReference.trim() || isLoading"
                            @click="submitScan"
                        >
                            {{ isLoading ? "Checking in…" : "Check In" }}
                        </Button>
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <div
                        class="rounded-xl border bg-card overflow-hidden flex flex-col min-h-48"
                    >
                        <div
                            v-if="!scanResult"
                            class="flex-1 flex flex-col items-center justify-center gap-4 p-8 text-center"
                        >
                            <div
                                class="size-16 rounded-full bg-muted flex items-center justify-center"
                            >
                                <ScanLine
                                    class="size-7 text-muted-foreground"
                                />
                            </div>
                            <div>
                                <p class="font-medium text-foreground">
                                    Awaiting scan
                                </p>
                                <p class="text-sm text-muted-foreground mt-1">
                                    Scan a QR code or enter a booking reference
                                    to check in an attendee.
                                </p>
                            </div>
                        </div>

                        <div
                            v-else-if="scanResult.success"
                            class="flex-1 flex flex-col items-center justify-center gap-6 p-8 text-center bg-green-50 dark:bg-green-950/20"
                        >
                            <CheckCircle2 class="size-16 text-green-500" />
                            <div class="space-y-1">
                                <p
                                    class="text-2xl font-semibold text-green-700 dark:text-green-400"
                                >
                                    Check-In Successful
                                </p>
                                <p
                                    class="text-sm text-green-600/80 dark:text-green-500/80"
                                >
                                    {{ scanResult.message }}
                                </p>
                            </div>
                            <div
                                v-if="scanResult.attendee_name"
                                class="w-full max-w-xs rounded-lg border border-green-200 dark:border-green-800 bg-white dark:bg-green-950/40 p-4 space-y-1"
                            >
                                <p
                                    class="text-xs uppercase tracking-wide text-green-600/70 dark:text-green-500/70"
                                >
                                    Attendee
                                </p>
                                <p
                                    class="font-semibold text-green-800 dark:text-green-300"
                                >
                                    {{ scanResult.attendee_name }}
                                </p>
                                <p
                                    v-if="scanResult.ticket_type"
                                    class="text-sm text-green-600 dark:text-green-400"
                                >
                                    {{ scanResult.ticket_type }}
                                </p>
                            </div>
                        </div>

                        <div
                            v-else
                            class="flex-1 flex flex-col items-center justify-center gap-6 p-8 text-center bg-destructive/5"
                        >
                            <XCircle class="size-16 text-destructive" />
                            <div class="space-y-1">
                                <p
                                    class="text-2xl font-semibold text-destructive"
                                >
                                    Check-In Failed
                                </p>
                                <p class="text-sm text-destructive/70">
                                    {{ scanResult.message }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="liveFeed.length > 0"
                        class="rounded-xl border bg-card overflow-hidden"
                    >
                        <div class="px-4 py-3 border-b">
                            <p
                                class="text-xs font-medium uppercase tracking-wide text-muted-foreground"
                            >
                                Live Feed
                            </p>
                        </div>
                        <ul class="divide-y max-h-64 overflow-y-auto">
                            <li
                                v-for="(entry, index) in liveFeed"
                                :key="index"
                                class="flex items-center gap-3 px-4 py-3"
                            >
                                <CheckCircle2
                                    class="size-4 text-green-500 shrink-0"
                                />
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium truncate">
                                        {{ entry.attendee }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ entry.ticketType }}
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
