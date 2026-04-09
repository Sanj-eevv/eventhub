<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { AlertTriangle, ArrowRight } from "lucide-vue-next";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import { index as eventsIndex } from "@/wayfinder/routes/dashboard/events";
import { formatDate } from "@/lib/utils";
import type { DraftEvent } from "@/types/dashboard";

defineProps<{
    events: DraftEvent[];
}>();
</script>

<template>
    <div>
        <p
            class="font-display mb-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground"
        >
            Needs Attention
        </p>
        <div
            class="overflow-hidden rounded-xl border border-amber-200 bg-amber-50 dark:border-amber-500/20 dark:bg-amber-500/5"
        >
            <div
                class="flex items-center gap-2.5 border-b border-amber-200 px-5 py-3 dark:border-amber-500/20"
            >
                <AlertTriangle
                    class="h-4 w-4 text-amber-600 dark:text-amber-400"
                />
                <p
                    class="text-sm font-medium text-amber-800 dark:text-amber-300"
                >
                    Unpublished events starting within 7 days
                </p>
            </div>
            <Table>
                <TableHeader>
                    <TableRow
                        class="border-amber-200 dark:border-amber-500/20"
                    >
                        <TableHead>Event</TableHead>
                        <TableHead>Starts At</TableHead>
                        <TableHead class="w-32 text-right">Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow
                        v-for="event in events"
                        :key="event.uuid"
                        class="border-amber-200 dark:border-amber-500/20"
                    >
                        <TableCell class="font-medium">{{
                            event.title
                        }}</TableCell>
                        <TableCell class="text-muted-foreground">{{
                            formatDate(event.starts_at)
                        }}</TableCell>
                        <TableCell class="text-right">
                            <Link
                                :href="eventsIndex().url"
                                class="inline-flex items-center gap-1 text-xs font-medium text-amber-700 hover:text-amber-900 dark:text-amber-400 dark:hover:text-amber-200"
                            >
                                Publish
                                <ArrowRight class="h-3 w-3" />
                            </Link>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </div>
</template>
