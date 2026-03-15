<script setup lang="ts">
import { Badge } from "@/components/ui/badge";
import type { OrganizationStatus, OrganizationStatusConfig } from "@/types/organization";

defineProps<{
    status: OrganizationStatus;
}>();

const statusConfig: OrganizationStatusConfig = {
    approved: { variant: "default", class: "bg-green-600 text-white hover:bg-green-600/90", label: "Approved" },
    suspended: { variant: "destructive", class: "", label: "Suspended" },
    pending: { variant: "default", class: "bg-blue-600 text-white", label: "Pending" },
    rejected: { variant: "default", class: "bg-yellow-500 text-white", label: "Rejected" },
};
</script>

<template>
    <Badge :variant="statusConfig[status].variant" :class="statusConfig[status].class">
        {{ statusConfig[status].label }}
    </Badge>
</template>
