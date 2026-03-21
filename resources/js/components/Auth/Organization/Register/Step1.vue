<script lang="ts" setup>
import type { FormComponentRef } from "@inertiajs/core";
import { useFormContext } from "@inertiajs/vue3";
import InputError from "@/components/InputError.vue";

const form = useFormContext() as FormComponentRef;
const emit = defineEmits<{
    next: [];
}>();
</script>

<template>
    <div class="grid gap-5">
        <div class="grid gap-1.5">
            <label for="title" class="font-body text-xs font-medium text-sf-muted uppercase tracking-wider required">
                Organization name
            </label>
            <input
                id="title"
                name="title"
                type="text"
                required
                autofocus
                :tabindex="1"
                placeholder="Organization name"
                class="w-full bg-sf-bg border border-sf-border rounded-lg px-4 py-2.5 font-body text-sm text-sf-text placeholder:text-sf-tertiary focus:outline-none focus:border-sf-gold transition-colors duration-200"
            />
            <InputError :message="form.errors.title" />
        </div>

        <div class="grid gap-1.5">
            <label for="description" class="font-body text-xs font-medium text-sf-muted uppercase tracking-wider required">
                Description
            </label>
            <textarea
                id="description"
                name="description"
                required
                :tabindex="2"
                placeholder="What does your organization do?"
                rows="3"
                class="w-full bg-sf-bg border border-sf-border rounded-lg px-4 py-2.5 font-body text-sm text-sf-text placeholder:text-sf-tertiary focus:outline-none focus:border-sf-gold transition-colors duration-200 resize-none"
            />
            <InputError :message="form.errors.description" />
        </div>

        <div class="grid gap-1.5">
            <label for="contact_address" class="font-body text-xs font-medium text-sf-muted uppercase tracking-wider required">
                Contact address
            </label>
            <input
                id="contact_address"
                name="contact_address"
                type="text"
                required
                :tabindex="3"
                placeholder="Organization address"
                class="w-full bg-sf-bg border border-sf-border rounded-lg px-4 py-2.5 font-body text-sm text-sf-text placeholder:text-sf-tertiary focus:outline-none focus:border-sf-gold transition-colors duration-200"
            />
            <InputError :message="form.errors.contact_address" />
        </div>

        <div class="grid gap-1.5">
            <label for="contact_email" class="font-body text-xs font-medium text-sf-muted uppercase tracking-wider required">
                Contact email
            </label>
            <input
                id="contact_email"
                name="contact_email"
                type="email"
                required
                :tabindex="4"
                placeholder="contact@organization.com"
                class="w-full bg-sf-bg border border-sf-border rounded-lg px-4 py-2.5 font-body text-sm text-sf-text placeholder:text-sf-tertiary focus:outline-none focus:border-sf-gold transition-colors duration-200"
            />
            <InputError :message="form.errors.contact_email" />
        </div>

        <button
            type="button"
            :tabindex="5"
            :disabled="form.validating"
            class="mt-2 w-full py-3 rounded-lg bg-sf-ember text-white font-body text-sm tracking-wide hover:bg-sf-ember-hover active:scale-[0.99] transition-all duration-200 disabled:opacity-40 disabled:pointer-events-none flex items-center justify-center gap-2"
            @click="form.validate({ only: ['title', 'description', 'contact_address', 'contact_email'], onSuccess: () => emit('next') })"
        >
            <svg v-if="form.validating" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
            </svg>
            Continue
        </button>
    </div>
</template>
