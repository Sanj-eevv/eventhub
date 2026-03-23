<script setup lang="ts">
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import { Badge } from "@/components/ui/badge";

const role = computed(
    () =>
        usePage<{ auth: { role: { name: string; slug: string } | null } }>()
            .props.auth.role,
);
</script>

<template>
    <Badge v-if="role" variant="secondary" class="text-[10px] px-1.5 py-0">
        {{ role.name }}
    </Badge>
</template>
