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
import type { User } from "@/types/user";
import { destroy } from "@/wayfinder/routes/dashboard/users";

const props = defineProps<{
    open: boolean;
    user: User;
}>();

const emit = defineEmits<{
    "update:open": [value: boolean];
}>();

const processing = ref(false);

const handleDelete = () => {
    processing.value = true;
    router.delete(destroy({ user: props.user.uuid }), {
        preserveScroll: true,
        onStart: () => {
            processing.value = true;
        },
        onSuccess: () => {
            emit("update:open", false);
        },
        onError: () => {
            toast.error("Failed to delete user. Please try again.");
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
                <AlertDialogTitle>Delete User</AlertDialogTitle>
                <AlertDialogDescription>
                    Are you sure you want to delete
                    <strong>{{ user.name }}</strong> ({{ user.email }})? This
                    action cannot be undone.
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
