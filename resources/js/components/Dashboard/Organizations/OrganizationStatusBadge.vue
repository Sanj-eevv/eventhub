<script setup lang="ts">
import type { VariantProps } from "class-variance-authority";
import { Badge } from "@/components/ui/badge";
import type { badgeVariants } from "@/components/ui/badge";
import type {
    OrganizationStatus,
    OrganizationStatusData,
} from "@/types/organization";

defineProps<{
    status: OrganizationStatusData;
}>();

type BadgeVariants = VariantProps<typeof badgeVariants>;
const statusConfig: Record<
    OrganizationStatus,
    { variant: BadgeVariants["variant"]; class: string }
> = {
    approved: {
        variant: "default",
        class: "bg-green-600 text-white hover:bg-green-600/90",
    },
    suspended: { variant: "destructive", class: "" },
    pending: { variant: "default", class: "bg-blue-600 text-white" },
    rejected: { variant: "default", class: "bg-yellow-500 text-white" },
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
