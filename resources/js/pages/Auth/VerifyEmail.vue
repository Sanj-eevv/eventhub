<script setup lang="ts">
import { Form, Head, Link } from "@inertiajs/vue3";
import AuthLayout from "@/layouts/AuthLayout.vue";
import { logout } from "@/wayfinder/routes/auth";
import { send } from "@/wayfinder/routes/auth/verification";

defineProps<{
    status?: string;
}>();
</script>

<template>
    <AuthLayout
        title="Verify your email"
        description="Thanks for signing up! Before getting started, please verify your email address by clicking the link we sent you."
    >
        <Head title="Email verification" />

        <div v-if="status === 'verification-link-sent'" class="mb-6 rounded-lg bg-sf-gold/10 border border-sf-gold/30 px-4 py-3 font-body text-sm text-sf-gold text-center">
            A new verification link has been sent to your email address.
        </div>

        <Form v-bind="send.form()" v-slot="{ processing }" class="flex flex-col gap-4 text-center">
            <button
                type="submit"
                :disabled="processing"
                class="w-full py-3 rounded-lg bg-sf-ember text-white font-body text-sm tracking-wide hover:bg-sf-ember-hover active:scale-[0.99] transition-all duration-200 disabled:opacity-40 disabled:pointer-events-none flex items-center justify-center gap-2"
            >
                <svg v-if="processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
                {{ processing ? 'Sending…' : 'Resend verification email' }}
            </button>

            <Link :href="logout()" class="font-body text-sm text-sf-muted hover:text-sf-text transition-colors">
                Log out
            </Link>
        </Form>
    </AuthLayout>
</template>
