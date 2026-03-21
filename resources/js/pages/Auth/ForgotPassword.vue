<script setup lang="ts">
import { Form, Head, Link } from "@inertiajs/vue3";
import InputError from "@/components/InputError.vue";
import AuthLayout from "@/layouts/AuthLayout.vue";
import { login } from "@/wayfinder/routes/auth";
import { email } from "@/wayfinder/routes/auth/password";

defineProps<{
    status?: string;
}>();
</script>

<template>
    <AuthLayout
        title="Forgot your password?"
        description="Enter your email and we'll send you a reset link"
    >
        <Head title="Forgot password" />

        <div v-if="status" class="mb-6 rounded-lg bg-sf-gold/10 border border-sf-gold/30 px-4 py-3 font-body text-sm text-sf-gold text-center">
            {{ status }}
        </div>

        <Form v-bind="email.form()" v-slot="{ errors, processing }" class="flex flex-col gap-5">
            <div class="grid gap-1.5">
                <label for="email" class="font-body text-xs font-medium text-sf-muted uppercase tracking-wider">
                    Email address
                </label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    autocomplete="off"
                    autofocus
                    placeholder="you@example.com"
                    class="w-full bg-sf-bg border border-sf-border rounded-lg px-4 py-2.5 font-body text-sm text-sf-text placeholder:text-sf-tertiary focus:outline-none focus:border-sf-gold transition-colors duration-200"
                />
                <InputError :message="errors.email" />
            </div>

            <button
                type="submit"
                :disabled="processing"
                data-test="email-password-reset-link-button"
                class="mt-2 w-full py-3 rounded-lg bg-sf-ember text-white font-body text-sm tracking-wide hover:bg-sf-ember-hover active:scale-[0.99] transition-all duration-200 disabled:opacity-40 disabled:pointer-events-none flex items-center justify-center gap-2"
            >
                <svg v-if="processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
                {{ processing ? 'Sending…' : 'Send reset link' }}
            </button>

            <p class="text-center font-body text-sm text-sf-muted">
                Remembered it?
                <Link :href="login()" class="text-sf-gold hover:text-sf-text transition-colors ml-1">
                    Back to sign in
                </Link>
            </p>
        </Form>
    </AuthLayout>
</template>
