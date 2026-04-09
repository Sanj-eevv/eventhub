<script setup lang="ts">
import { formatDateTime } from "@/lib/utils";
import type { ActivityItem } from "@/types/activity";

defineProps<{
    items: ActivityItem[];
}>();

const eventColors: Record<string, string> = {
    "event.published": "bg-emerald-500",
    "event.cancelled": "bg-red-500",
    "organization.approved": "bg-blue-500",
    "organization.rejected": "bg-orange-500",
    "order.cancelled": "bg-red-400",
    "refund.processed": "bg-violet-500",
};
</script>

<template>
    <div>
        <p
            class="font-display mb-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground"
        >
            Recent Activity
        </p>
        <div class="overflow-hidden rounded-xl border border-border bg-card">
            <div
                v-if="items.length === 0"
                class="flex items-center justify-center px-4 py-10 text-sm text-muted-foreground"
            >
                No activity recorded yet.
            </div>
            <ul v-else class="divide-y divide-border">
                <li
                    v-for="item in items"
                    :key="item.uuid"
                    class="flex items-start gap-3 px-4 py-3"
                >
                    <span
                        :class="[
                            'mt-1.5 h-2 w-2 shrink-0 rounded-full',
                            eventColors[item.event.value] ?? 'bg-muted-foreground',
                        ]"
                    />
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-foreground">
                            {{ item.event.label }}
                            <span
                                v-if="item.subject_label"
                                class="font-normal text-muted-foreground"
                            >
                                — {{ item.subject_label }}
                            </span>
                        </p>
                        <p class="text-xs text-muted-foreground">
                            <span v-if="item.causer_name">{{ item.causer_name }} · </span>
                            {{ formatDateTime(item.created_at) }}
                        </p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>
