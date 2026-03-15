<script setup lang="ts">
import { ChevronDownIcon } from "lucide-vue-next";
import { Button } from "@/components/ui/button";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { usePermission } from "@/composables/usePermission";
import type { Organization } from "@/types/organization";

defineProps<{
    organization: Organization;
}>();

const can = usePermission("organization");
const emit = defineEmits<{
    edit: [];
    approve: [];
    reject: [];
    delete: [];
}>();
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="outline" size="sm">
                Actions
                <ChevronDownIcon class="size-4" />
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <template v-if="organization.status === 'pending'">
                <DropdownMenuItem @click="emit('approve')">
                    Approve
                </DropdownMenuItem>
                <DropdownMenuItem
                    class="text-destructive focus:text-destructive"
                    @click="emit('reject')"
                >
                    Reject
                </DropdownMenuItem>
                <DropdownMenuSeparator />
            </template>
            <DropdownMenuItem v-if="can('update')" @click="emit('edit')"
                >Edit</DropdownMenuItem
            >
            <DropdownMenuItem
                v-if="can('delete')"
                class="text-destructive focus:text-destructive"
                @click="emit('delete')"
            >
                Delete
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
