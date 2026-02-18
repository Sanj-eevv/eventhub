<script lang="ts" setup>
import type { FormComponentRef } from "@inertiajs/core";
import { useFormContext } from "@inertiajs/vue3";
import InputError from "@/components/InputError.vue";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Spinner } from "@/components/ui/spinner";
import { Textarea } from "@/components/ui/textarea";

const form = useFormContext() as FormComponentRef;
const emit = defineEmits<{
    next: [];
}>();
</script>
<template>
    <div class="grid gap-6">
        <div class="grid gap-2">
            <Label class="required" for="title">Organization name </Label>
            <Input
                id="title"
                name="title"
                type="text"
                required
                autofocus
                :tabindex="1"
                placeholder="Organization name"
            />
            <InputError :message="form.errors.title" />
        </div>
        <div class="grid gap-2">
            <Label class="required" for="description">Description</Label>
            <Textarea
                id="description"
                name="description"
                required
                :tabindex="2"
                placeholder="What does your organization do?"
                rows="3"
            />
            <InputError :message="form.errors.description" />
        </div>
        <div class="grid gap-2">
            <Label class="required" for="contact_address"
                >Contact address</Label
            >
            <Input
                id="contact_address"
                name="contact_address"
                type="text"
                required
                :tabindex="3"
                placeholder="Organization address"
            />
            <InputError :message="form.errors.contact_address" />
        </div>
        <div class="grid gap-2">
            <Label class="required" for="contact_email">Contact email</Label>
            <Input
                id="contact_email"
                name="contact_email"
                type="email"
                required
                :tabindex="4"
                placeholder="Organization contact email"
            />
            <InputError :message="form.errors.contact_email" />
        </div>
        <Button
            type="button"
            class="mt-2 w-full"
            :tabindex="5"
            :disabled="form.validating"
            @click="
                form.validate({
                    only: [
                        'title',
                        'description',
                        'contact_address',
                        'contact_email',
                    ],
                    onSuccess: () => emit('next'),
                })
            "
        >
            Continue
            <Spinner v-if="form.validating" />
        </Button>
    </div>
</template>
