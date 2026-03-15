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
import type { Event } from "@/types/event";

defineProps<{
    event: Event;
}>();

const emit = defineEmits<{
    edit: [];
    publish: [];
    cancel: [];
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
            <template v-if="event.status === 'draft'">
                <DropdownMenuItem @click="emit('publish')">
                    Publish
                </DropdownMenuItem>
                <DropdownMenuSeparator />
            </template>
            <template v-else-if="event.status === 'published'">
                <DropdownMenuItem
                    class="text-destructive focus:text-destructive"
                    @click="emit('cancel')"
                >
                    Cancel Event
                </DropdownMenuItem>
                <DropdownMenuSeparator />
            </template>
            <DropdownMenuItem @click="emit('edit')">Edit</DropdownMenuItem>
            <DropdownMenuSeparator />
            <DropdownMenuItem
                class="text-destructive focus:text-destructive"
                @click="emit('delete')"
            >
                Delete
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
