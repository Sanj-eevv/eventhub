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

defineProps<{
    canEdit: boolean;
    canDelete: boolean;
}>();
const can = usePermission("role");

const emit = defineEmits<{
    edit: [];
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
            <DropdownMenuItem :disabled="!can('update')" @click="emit('edit')">
                Edit
            </DropdownMenuItem>
            <DropdownMenuSeparator />
            <DropdownMenuItem
                :disabled="!can('delete')"
                class="text-destructive focus:text-destructive"
                @click="emit('delete')"
            >
                Delete
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
