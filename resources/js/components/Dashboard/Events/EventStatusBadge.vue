<script setup lang="ts">
import type { VariantProps } from "class-variance-authority";
import { Badge } from "@/components/ui/badge";
import type { badgeVariants } from "@/components/ui/badge";
import type { EventStatus, EventStatusData } from "@/types/event";

defineProps<{
    status: EventStatusData;
}>();

type BadgeVariants = VariantProps<typeof badgeVariants>;
const statusConfig: Record<
    EventStatus,
    { variant: BadgeVariants["variant"]; class: string }
> = {
    draft: { variant: "default", class: "bg-blue-600 text-white" },
    published: {
        variant: "default",
        class: "bg-green-600 text-white hover:bg-green-600/90",
    },
    cancelled: { variant: "destructive", class: "" },
};
</script>

<template>
    <Badge
        :variant="statusConfig[status.value].variant"
        :class="statusConfig[status.value].class"
    >
        {{ status.label }}
    </Badge>
</template>
