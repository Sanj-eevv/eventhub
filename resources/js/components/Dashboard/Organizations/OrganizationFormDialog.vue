<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import { computed } from "vue";
import { toast } from "vue-sonner";
import InputError from "@/components/InputError.vue";
import { Button } from "@/components/ui/button";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { Spinner } from "@/components/ui/spinner";
import { Textarea } from "@/components/ui/textarea";
import type { Organization } from "@/types/organization";
import { organizationStatusLabels } from "@/constants/statusLabels";
import { store, update } from "@/wayfinder/routes/dashboard/organizations";

const props = defineProps<{
    open: boolean;
    organization: Organization | null;
}>();

const emit = defineEmits<{
    "update:open": [value: boolean];
}>();

const isEditing = computed(() => !!props.organization);

const formConfig = computed(() => {
    const editing = isEditing.value;
    return {
        title: editing ? "Edit Organization" : "Create Organization",
        descritption: editing
            ? "Update the organization details below."
            : "Fill in the details to create a new organization.",
        submitButtonText: editing ? "Update" : "Create",
    };
});


const form = useForm({
    title: props.organization?.title || "",
    description: props.organization?.description || "",
    contact_address: props.organization?.contact_address || "",
    contact_email: props.organization?.contact_email || "",
    status: props.organization?.status || "approved",
});

const updateOrganization = () => {
    const organization = props.organization as Organization;
    form.put(update({ organization: organization.uuid }).url, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            emit("update:open", false);
        },
        onError: () => {
            toast.error(
                "An error occurred while updating the organization. Please try again.",
            );
        },
    });
};

const createOrganization = () => {
    form.post(store().url, {
        onSuccess: () => {
            emit("update:open", false);
        },
        onError: () => {
            toast.error(
                "An error occurred while creating the organization. Please try again.",
            );
        },
    });
};
</script>

<template>
    <Dialog
        :open="open"
        @update:open="(val: boolean) => emit('update:open', val)"
    >
        <DialogContent class="sm:max-w-lg">
            <DialogHeader>
                <DialogTitle>{{ formConfig.title }}</DialogTitle>
                <DialogDescription>
                    {{ formConfig.descritption }}
                </DialogDescription>
            </DialogHeader>

            <form
                @submit.prevent="
                    (isEditing ? updateOrganization : createOrganization)()
                "
                class="space-y-4"
            >
                <div class="grid gap-2">
                    <Label for="org-title">Title</Label>
                    <Input id="org-title" v-model="form.title" type="text" />
                    <InputError :message="form.errors.title" />
                </div>

                <div class="grid gap-2">
                    <Label for="org-description">Description</Label>
                    <Textarea
                        id="org-description"
                        v-model="form.description"
                        rows="3"
                    />
                    <InputError :message="form.errors.description" />
                </div>

                <div class="grid gap-2">
                    <Label for="org-address">Contact Address</Label>
                    <Input
                        id="org-address"
                        v-model="form.contact_address"
                        type="text"
                    />
                    <InputError :message="form.errors.contact_address" />
                </div>

                <div class="grid gap-2">
                    <Label for="org-email">Contact Email</Label>
                    <Input
                        id="org-email"
                        v-model="form.contact_email"
                        type="text"
                    />
                    <InputError :message="form.errors.contact_email" />
                </div>

                <div class="grid gap-2">
                    <Label for="org-status">Status</Label>
                    <Select v-model="form.status">
                        <SelectTrigger>
                            <SelectValue placeholder="Select status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="(label, value) in organizationStatusLabels"
                                :key="value"
                                :value="value"
                                >{{ label }}</SelectItem
                            >
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.status" />
                </div>

                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        @click="emit('update:open', false)"
                    >
                        Cancel
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        <Spinner v-if="form.processing" />
                        {{ formConfig.submitButtonText }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
