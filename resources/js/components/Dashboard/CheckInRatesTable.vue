<script setup lang="ts">
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import { formatDate } from "@/lib/utils";

interface EventCheckInRate {
    uuid: string;
    title: string;
    starts_at: string;
    total_tickets: number;
    checked_in_count: number;
}

defineProps<{
    events: EventCheckInRate[];
}>();

function checkInPercent(event: EventCheckInRate): number {
    if (event.total_tickets === 0) return 0;
    return Math.round((event.checked_in_count / event.total_tickets) * 100);
}

function checkInBarColor(percent: number): string {
    if (percent >= 70) return "bg-emerald-500";
    if (percent >= 30) return "bg-primary";
    return "bg-muted-foreground/40";
}
</script>

<template>
    <div>
        <p
            class="font-display mb-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground"
        >
            Upcoming Events — Check-in
        </p>
        <div
            class="overflow-hidden rounded-xl border border-border bg-card"
        >
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Event</TableHead>
                        <TableHead>Date</TableHead>
                        <TableHead class="w-48">Progress</TableHead>
                        <TableHead class="w-20 text-right">Rate</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="event in events" :key="event.uuid">
                        <TableCell class="font-medium">{{
                            event.title
                        }}</TableCell>
                        <TableCell class="text-muted-foreground">{{
                            formatDate(event.starts_at)
                        }}</TableCell>
                        <TableCell>
                            <div
                                v-if="event.total_tickets > 0"
                                class="flex items-center gap-2.5"
                            >
                                <div
                                    class="h-1.5 flex-1 overflow-hidden rounded-full bg-muted"
                                >
                                    <div
                                        class="h-full rounded-full transition-all duration-500"
                                        :class="
                                            checkInBarColor(
                                                checkInPercent(event),
                                            )
                                        "
                                        :style="{
                                            width: `${checkInPercent(event)}%`,
                                        }"
                                    />
                                </div>
                                <span
                                    class="w-20 text-right text-xs text-muted-foreground"
                                >
                                    {{ event.checked_in_count }} /
                                    {{ event.total_tickets }}
                                </span>
                            </div>
                            <span v-else class="text-muted-foreground"
                                >—</span
                            >
                        </TableCell>
                        <TableCell class="text-right">
                            <span
                                v-if="event.total_tickets > 0"
                                class="font-display text-sm font-semibold"
                            >
                                {{ checkInPercent(event) }}%
                            </span>
                            <span v-else class="text-muted-foreground"
                                >—</span
                            >
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </div>
</template>
