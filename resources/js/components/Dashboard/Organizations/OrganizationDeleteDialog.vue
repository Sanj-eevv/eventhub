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
import type { Organization } from "@/types/organization";
import { destroy } from "@/wayfinder/routes/dashboard/organizations";

const props = defineProps<{
    open: boolean;
    organization: Organization;
}>();

const emit = defineEmits<{
    "update:open": [value: boolean];
}>();

const processing = ref(false);

const handleDelete = () => {
    router.delete(destroy({ organization: props.organization.uuid }), {
        preserveScroll: true,
        onStart: () => {
            processing.value = true;
        },
        onSuccess: () => {
            emit("update:open", false);
        },
        onError: () => {
            toast.error("Failed to delete organization. Please try again.");
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
                <AlertDialogTitle>Delete Organization</AlertDialogTitle>
                <AlertDialogDescription>
                    Are you sure you want to delete "{{
                        props.organization.title
                    }}"? All users associated with this organization will also
                    be permanently deleted. This action cannot be undone.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel :disabled="processing">
                    Cancel
                </AlertDialogCancel>
                <AlertDialogAction
                    class="bg-destructive text-white hover:bg-destructive/90"
                    :disabled="processing"
                    @click="handleDelete"
                >
                    Delete
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
