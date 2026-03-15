<script setup lang="ts">
import { ChevronDownIcon } from "lucide-vue-next";
import { Button } from "@/components/ui/button";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { usePermission } from "@/composables/usePermission";

const emit = defineEmits<{
    edit: [];
    delete: [];
}>();
const can = usePermission("user");
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
