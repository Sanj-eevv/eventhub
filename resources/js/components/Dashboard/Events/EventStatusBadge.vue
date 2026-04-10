<script setup lang="ts">
import { Badge } from "@/components/ui/badge";
import type { EventStatusData } from "@/types/event";

defineProps<{
    status: EventStatusData;
}>();

const colorClass: Record<string, string> = {
    green: "bg-green-600 text-white hover:bg-green-600/90",
    blue: "bg-blue-600 text-white hover:bg-blue-600/90",
    yellow: "bg-yellow-500 text-white hover:bg-yellow-500/90",
    red: "",
    gray: "bg-gray-500 text-white hover:bg-gray-500/90",
};
</script>

<template>
    <Badge
        :variant="status.color === 'red' ? 'destructive' : 'default'"
        :class="colorClass[status.color]"
    >
        {{ status.label }}
    </Badge>
</template>
