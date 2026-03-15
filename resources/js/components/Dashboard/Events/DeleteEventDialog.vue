<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import { ref } from "vue";
import { toast } from "vue-sonner";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from "@/components/ui/alert-dialog";
import type { Event } from "@/types/event";
import { destroy } from "@/wayfinder/routes/dashboard/events";

const props = defineProps<{
    open: boolean;
    event: Event;
}>();

const emit = defineEmits<{
    "update:open": [value: boolean];
}>();

const processing = ref(false);

const handleDelete = () => {
    router.delete(destroy({ event: props.event.uuid }), {
        preserveScroll: true,
        onStart: () => {
            processing.value = true;
        },
        onSuccess: () => {
            emit("update:open", false);
        },
        onError: () => {
            toast.error("Failed to delete event. Please try again.");
        },
        onFinish: () => {
            processing.value = false;
        },
    });
};
</script>

<template>
    <AlertDialog
        :open="open"
        @update:open="(val: boolean) => emit('update:open', val)"
    >
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Delete Event</AlertDialogTitle>
                <AlertDialogDescription>
                    Are you sure you want to delete
                    <strong>{{ event.title }}</strong>? This action cannot be
                    undone.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel :disabled="processing">
                    Cancel
                </AlertDialogCancel>
                <AlertDialogAction :disabled="processing" @click="handleDelete">
                    Delete
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
