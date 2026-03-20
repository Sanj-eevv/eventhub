<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import { ref } from "vue";
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
import { Checkbox } from "@/components/ui/checkbox";
import { Label } from "@/components/ui/label";
import type { Organization } from "@/types/organization";
import { approve, reject } from "@/wayfinder/routes/dashboard/organizations";

const props = defineProps<{
    open: boolean;
    organization: Organization;
    action: "approve" | "reject";
}>();

const emit = defineEmits<{
    "update:open": [value: boolean];
}>();

const processing = ref(false);
const sendNotification = ref(true);

const handleConfirm = () => {
    const route = props.action === "approve"
        ? approve({ organization: props.organization.uuid })
        : reject({ organization: props.organization.uuid });

    router.post(
        route.url,
        { send_notification: sendNotification.value },
        {
            preserveScroll: true,
            onStart: () => {
                processing.value = true;
            },
            onSuccess: () => {
                emit("update:open", false);
            },
            onError: () => {},
            onFinish: () => {
                processing.value = false;
            },
        },
    );
};
</script>

<template>
    <AlertDialog
        :open="open"
        @update:open="(val: boolean) => emit('update:open', val)"
    >
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle class="capitalize"
                    >{{ props.action }} Organization</AlertDialogTitle
                >
                <AlertDialogDescription>
                    Are you sure you want to {{ props.action }} "{{
                        props.organization.title
                    }}"? An approval email will be sent to
                    {{ props.organization.contact_email }}.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <div class="flex items-center gap-2">
                <Checkbox
                    id="send-notification"
                    v-model="sendNotification"
                    :disabled="processing"
                />
                <Label
                    for="send-notification"
                    class="cursor-pointer text-sm font-normal"
                >
                    Send email notification
                </Label>
            </div>
            <AlertDialogFooter>
                <AlertDialogCancel :disabled="processing">
                    Cancel
                </AlertDialogCancel>
                <AlertDialogAction
                    class="capitalize"
                    :disabled="processing"
                    :class="{
                        'bg-destructive text-white hover:bg-destructive/90':
                            props.action === 'reject',
                    }"
                    @click="handleConfirm"
                >
                    {{ props.action }}
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
