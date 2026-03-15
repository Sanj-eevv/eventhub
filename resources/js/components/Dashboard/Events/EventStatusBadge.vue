<script setup lang="ts">
import { Badge } from "@/components/ui/badge";
import type { EventStatus, EventStatusConfig } from "@/types/event";

defineProps<{
    status: EventStatus;
}>();

const statusConfig: EventStatusConfig = {
    draft: {
        variant: "default",
        class: "bg-blue-600 text-white",
        label: "Draft",
    },
    published: {
        variant: "default",
        class: "bg-green-600 text-white hover:bg-green-600/90",
        label: "Published",
    },
    cancelled: { variant: "destructive", class: "", label: "Cancelled" },
};
</script>

<template>
    <Badge
        :variant="statusConfig[status].variant"
        :class="statusConfig[status].class"
    >
        {{ statusConfig[status].label }}
    </Badge>
</template>
