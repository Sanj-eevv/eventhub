<script lang="ts" setup>
import type { FormComponentRef } from "@inertiajs/core";
import { useFormContext } from "@inertiajs/vue3";
import InputError from "@/components/InputError.vue";

const form = useFormContext() as FormComponentRef;
const emit = defineEmits<{
    back: [];
}>();
</script>

<template>
    <div class="grid gap-5">
        <div class="grid gap-1.5">
            <label for="name" class="font-body text-xs font-medium text-sf-muted uppercase tracking-wider">
                Your name
            </label>
            <input
                id="name"
                name="name"
                type="text"
                required
                :tabindex="1"
                autocomplete="name"
                placeholder="Full name"
                class="w-full bg-sf-bg border border-sf-border rounded-lg px-4 py-2.5 font-body text-sm text-sf-text placeholder:text-sf-tertiary focus:outline-none focus:border-sf-gold transition-colors duration-200"
            />
            <InputError :message="form.errors.name" />
        </div>

        <div class="grid gap-1.5">
            <label for="email" class="font-body text-xs font-medium text-sf-muted uppercase tracking-wider">
                Email address
            </label>
            <input
                id="email"
                name="email"
                type="email"
                required
                :tabindex="2"
                autocomplete="email"
                placeholder="you@example.com"
                class="w-full bg-sf-bg border border-sf-border rounded-lg px-4 py-2.5 font-body text-sm text-sf-text placeholder:text-sf-tertiary focus:outline-none focus:border-sf-gold transition-colors duration-200"
            />
            <InputError :message="form.errors.email" />
        </div>

        <div class="grid gap-1.5">
            <label for="password" class="font-body text-xs font-medium text-sf-muted uppercase tracking-wider">
                Password
            </label>
            <input
                id="password"
                name="password"
                type="password"
                required
                :tabindex="3"
                autocomplete="new-password"
                placeholder="••••••••"
                class="w-full bg-sf-bg border border-sf-border rounded-lg px-4 py-2.5 font-body text-sm text-sf-text placeholder:text-sf-tertiary focus:outline-none focus:border-sf-gold transition-colors duration-200"
            />
            <InputError :message="form.errors.password" />
        </div>

        <div class="grid gap-1.5">
            <label for="password_confirmation" class="font-body text-xs font-medium text-sf-muted uppercase tracking-wider">
                Confirm password
            </label>
            <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                required
                :tabindex="4"
                autocomplete="new-password"
                placeholder="••••••••"
                class="w-full bg-sf-bg border border-sf-border rounded-lg px-4 py-2.5 font-body text-sm text-sf-text placeholder:text-sf-tertiary focus:outline-none focus:border-sf-gold transition-colors duration-200"
            />
            <InputError :message="form.errors.password_confirmation" />
        </div>

        <div class="flex gap-3 mt-2">
            <button
                type="button"
                :tabindex="6"
                :disabled="form.processing"
                class="flex-1 py-3 rounded-lg border border-sf-border text-sf-muted font-body text-sm hover:border-sf-gold hover:text-sf-text transition-all duration-200 disabled:opacity-40 disabled:pointer-events-none"
                @click="emit('back')"
            >
                Back
            </button>
            <button
                type="submit"
                :tabindex="5"
                :disabled="form.processing"
                class="flex-1 py-3 rounded-lg bg-sf-ember text-white font-body text-sm tracking-wide hover:bg-sf-ember-hover active:scale-[0.99] transition-all duration-200 disabled:opacity-40 disabled:pointer-events-none flex items-center justify-center gap-2"
            >
                <svg v-if="form.processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
                Create account
            </button>
        </div>
    </div>
</template>
