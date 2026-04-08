<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { ArrowRight } from "lucide-vue-next";
import { index as eventsIndex } from "@/wayfinder/routes/dashboard/events";

defineProps<{
    eventsByStatus: { draft: number; published: number; cancelled: number };
}>();
</script>

<template>
    <div>
        <p
            class="font-display mb-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground"
        >
            Events Overview
        </p>
        <div
            class="grid divide-y rounded-xl border border-border bg-card sm:grid-cols-3 sm:divide-x sm:divide-y-0"
        >
            <div class="flex items-center gap-4 p-5">
                <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-amber-500" />
                <div>
                    <p
                        class="text-[11px] font-medium uppercase tracking-widest text-muted-foreground"
                    >
                        Draft
                    </p>
                    <p class="font-display mt-0.5 text-3xl font-semibold">
                        {{ eventsByStatus.draft }}
                    </p>
                </div>
                <Link
                    :href="eventsIndex().url + '?status=draft'"
                    class="ml-auto text-muted-foreground transition-colors hover:text-foreground"
                >
                    <ArrowRight class="h-4 w-4" />
                </Link>
            </div>

            <div class="flex items-center gap-4 p-5">
                <span
                    class="h-2.5 w-2.5 shrink-0 rounded-full bg-emerald-500"
                />
                <div>
                    <p
                        class="text-[11px] font-medium uppercase tracking-widest text-muted-foreground"
                    >
                        Published
                    </p>
                    <p class="font-display mt-0.5 text-3xl font-semibold">
                        {{ eventsByStatus.published }}
                    </p>
                </div>
                <Link
                    :href="eventsIndex().url + '?status=published'"
                    class="ml-auto text-muted-foreground transition-colors hover:text-foreground"
                >
                    <ArrowRight class="h-4 w-4" />
                </Link>
            </div>

            <div class="flex items-center gap-4 p-5">
                <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-destructive" />
                <div>
                    <p
                        class="text-[11px] font-medium uppercase tracking-widest text-muted-foreground"
                    >
                        Cancelled
                    </p>
                    <p class="font-display mt-0.5 text-3xl font-semibold">
                        {{ eventsByStatus.cancelled }}
                    </p>
                </div>
                <Link
                    :href="eventsIndex().url + '?status=cancelled'"
                    class="ml-auto text-muted-foreground transition-colors hover:text-foreground"
                >
                    <ArrowRight class="h-4 w-4" />
                </Link>
            </div>
        </div>
    </div>
</template>
