<script setup lang="ts">
import { Form, Head } from "@inertiajs/vue3";
import { ref } from "vue";
import InputError from "@/components/InputError.vue";
import AuthLayout from "@/layouts/AuthLayout.vue";
import { store } from "@/wayfinder/routes/auth/password";

const props = defineProps<{
    token: string;
    email: string;
}>();

const inputEmail = ref(props.email);
</script>

<template>
    <AuthLayout
        title="Reset your password"
        description="Enter a new password for your account"
    >
        <Head title="Reset password" />

        <Form
            v-bind="store.form()"
            :transform="(data) => ({ ...data, token, email })"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-5"
        >
            <div class="grid gap-1.5">
                <label for="email" class="font-body text-xs font-medium text-sf-muted uppercase tracking-wider">
                    Email address
                </label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    autocomplete="email"
                    v-model="inputEmail"
                    readonly
                    class="w-full bg-sf-surface border border-sf-border-subtle rounded-lg px-4 py-2.5 font-body text-sm text-sf-muted cursor-not-allowed focus:outline-none transition-colors duration-200"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-1.5">
                <label for="password" class="font-body text-xs font-medium text-sf-muted uppercase tracking-wider">
                    New password
                </label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    autocomplete="new-password"
                    autofocus
                    placeholder="••••••••"
                    class="w-full bg-sf-bg border border-sf-border rounded-lg px-4 py-2.5 font-body text-sm text-sf-text placeholder:text-sf-tertiary focus:outline-none focus:border-sf-gold transition-colors duration-200"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-1.5">
                <label for="password_confirmation" class="font-body text-xs font-medium text-sf-muted uppercase tracking-wider">
                    Confirm new password
                </label>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    autocomplete="new-password"
                    placeholder="••••••••"
                    class="w-full bg-sf-bg border border-sf-border rounded-lg px-4 py-2.5 font-body text-sm text-sf-text placeholder:text-sf-tertiary focus:outline-none focus:border-sf-gold transition-colors duration-200"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <button
                type="submit"
                :disabled="processing"
                data-test="reset-password-button"
                class="mt-2 w-full py-3 rounded-lg bg-sf-ember text-white font-body text-sm tracking-wide hover:bg-sf-ember-hover active:scale-[0.99] transition-all duration-200 disabled:opacity-40 disabled:pointer-events-none flex items-center justify-center gap-2"
            >
                <svg v-if="processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
                {{ processing ? 'Resetting…' : 'Reset password' }}
            </button>
        </Form>
    </AuthLayout>
</template>
